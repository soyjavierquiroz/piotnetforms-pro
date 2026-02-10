import { useEffect, useState } from 'react';

export type CountrySource = 'server' | 'ipapi' | 'locale' | 'timezone' | 'fallback';

const TZ_TO_COUNTRY: Record<string, string> = {
  'America/Mexico_City': 'mx',
  'America/Bogota': 'co',
  'America/Lima': 'pe',
  'America/Santiago': 'cl',
  'America/Argentina/Buenos_Aires': 'ar',
  'Europe/Madrid': 'es',
  'Europe/Paris': 'fr',
  'Europe/Berlin': 'de',
  'America/New_York': 'us',
};

const normalize = (value: string | undefined | null) =>
  value?.toLowerCase().trim().slice(0, 2);

export function useCountryAutoDetect(fallbackCountry = 'us') {
  const [country, setCountry] = useState(fallbackCountry);
  const [source, setSource] = useState<CountrySource>('fallback');

  useEffect(() => {
    let mounted = true;
    const controller = new AbortController();

    const apply = (code: string, src: CountrySource) => {
      if (!mounted) return;
      setCountry(code);
      setSource(src);
    };

    const detect = async () => {
      // Opción 1: endpoint propio (Cloudflare header o GeoIP server-side)
      try {
        const res = await fetch('/api/geo/country', { signal: controller.signal });
        if (res.ok) {
          const data = (await res.json()) as { countryCode?: string };
          const code = normalize(data.countryCode);
          if (code) return apply(code, 'server');
        }
      } catch {
        // continúa fallback
      }

      // Opción 2: proveedor externo gratis (ipapi)
      try {
        const res = await fetch('https://ipapi.co/json/', { signal: controller.signal });
        if (res.ok) {
          const data = (await res.json()) as { country_code?: string };
          const code = normalize(data.country_code);
          if (code) return apply(code, 'ipapi');
        }
      } catch {
        // continúa fallback
      }

      // Opción 3a: locale del navegador
      const localeRegion = normalize(navigator.language.split('-')[1]);
      if (localeRegion) return apply(localeRegion, 'locale');

      // Opción 3b: timezone heurístico
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
      const tzCountry = TZ_TO_COUNTRY[tz];
      if (tzCountry) return apply(tzCountry, 'timezone');

      apply(fallbackCountry, 'fallback');
    };

    detect();
    return () => {
      mounted = false;
      controller.abort();
    };
  }, [fallbackCountry]);

  return { country, source };
}

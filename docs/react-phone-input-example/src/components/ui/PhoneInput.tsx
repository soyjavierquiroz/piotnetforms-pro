import { useEffect } from 'react';
import { useController, type Control, type FieldValues, type Path } from 'react-hook-form';
import { PhoneInput as IntlPhoneInput } from 'react-international-phone';
import 'react-international-phone/style.css';
import { parsePhoneNumberFromString } from 'libphonenumber-js/min';
import { useCountryAutoDetect } from '../../hooks/useCountryAutoDetect';

type Props<T extends FieldValues> = {
  control: Control<T>;
  name: Path<T>;
  label: string;
  required?: boolean;
  fallbackCountry?: string;
};

export function PhoneInput<T extends FieldValues>({
  control,
  name,
  label,
  required,
  fallbackCountry = 'us',
}: Props<T>) {
  const { field, fieldState } = useController({ control, name });
  const { country } = useCountryAutoDetect(fallbackCountry);

  useEffect(() => {
    if (!field.value) return;
    const parsed = parsePhoneNumberFromString(field.value);
    if (parsed?.isValid()) field.onChange(parsed.number); // E.164
  }, [field]);

  return (
    <div className="space-y-1">
      <label className="text-sm font-medium text-slate-700">
        {label} {required ? <span className="text-red-500">*</span> : null}
      </label>
      <IntlPhoneInput
        defaultCountry={country}
        value={field.value ?? ''}
        onChange={(value) => {
          const parsed = parsePhoneNumberFromString(value);
          field.onChange(parsed?.isValid() ? parsed.number : value);
        }}
        inputClassName="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500"
        countrySelectorStyleProps={{
          buttonClassName: 'rounded-l-md border border-slate-300 bg-white',
          dropdownStyleProps: { className: 'text-sm' },
        }}
      />
      {fieldState.error?.message ? (
        <p className="text-xs text-red-600">{fieldState.error.message}</p>
      ) : null}
    </div>
  );
}

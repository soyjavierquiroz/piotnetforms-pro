import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { parsePhoneNumberFromString } from 'libphonenumber-js/min';
import { PhoneInput } from './components/ui/PhoneInput';
import { submitLead } from './lib/api';

const schema = z.object({
  fullName: z.string().min(2, 'Nombre muy corto'),
  phone: z
    .string()
    .min(6, 'Teléfono requerido')
    .refine((value) => !!parsePhoneNumberFromString(value)?.isValid(), 'Número inválido'),
});

type FormValues = z.infer<typeof schema>;

export default function App() {
  const form = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { fullName: '', phone: '' },
  });

  const onSubmit = form.handleSubmit(async (values) => {
    await submitLead({
      fullName: values.fullName,
      phoneE164: parsePhoneNumberFromString(values.phone)?.number ?? values.phone,
    });
    form.reset();
  });

  return (
    <main className="mx-auto max-w-xl p-6">
      <h1 className="mb-4 text-xl font-semibold">Demo PhoneInput</h1>
      <form onSubmit={onSubmit} className="space-y-4">
        <div className="space-y-1">
          <label className="text-sm font-medium text-slate-700">Nombre</label>
          <input
            {...form.register('fullName')}
            className="w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
          />
          {form.formState.errors.fullName?.message ? (
            <p className="text-xs text-red-600">{form.formState.errors.fullName.message}</p>
          ) : null}
        </div>

        <PhoneInput<FormValues> control={form.control} name="phone" label="Teléfono" required />

        <button
          type="submit"
          className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
        >
          Enviar
        </button>
      </form>
    </main>
  );
}

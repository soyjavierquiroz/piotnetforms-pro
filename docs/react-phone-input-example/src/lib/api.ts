export type SubmitLeadPayload = {
  fullName: string;
  phoneE164: string;
};

export async function submitLead(payload: SubmitLeadPayload) {
  const res = await fetch('/api/leads', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });

  if (!res.ok) {
    throw new Error('No se pudo enviar el formulario');
  }

  return (await res.json()) as { ok: true; id: string };
}

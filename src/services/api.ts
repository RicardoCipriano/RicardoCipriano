import type { Birthday } from "../data/birthdays";

const defaultBase = "http://localhost/aniversario/api";

export async function fetchBirthdays(
  scope: "today" | "month",
  base?: string,
): Promise<Birthday[]> {
  const apiBase = base ?? import.meta.env.VITE_API_BASE_URL ?? defaultBase;
  const url = `${apiBase}/birthdays.php?scope=${scope}`;
  try {
    const res = await fetch(url, {
      headers: { Accept: "application/json" },
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const json = await res.json();
    if (json && json.success && Array.isArray(json.data)) {
      return json.data as Birthday[];
    }
    throw new Error("Resposta inválida da API");
  } catch (err) {
    console.error("Erro ao buscar aniversários:", err);
    return [];
  }
}
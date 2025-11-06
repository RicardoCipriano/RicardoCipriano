/**
 * Calcula quantos dias faltam até um aniversário
 * @param dateStr - Data no formato "DD/MM"
 * @returns Número de dias até o aniversário
 */
export function daysUntilBirthday(dateStr: string): number {
  const [day, month] = dateStr.split('/').map(Number);
  
  const today = new Date();
  const currentYear = today.getFullYear();
  // Se o aniversário é hoje (mesmo dia e mês), retornar 0
  if (day === today.getDate() && month === today.getMonth() + 1) {
    return 0;
  }
  
  // Criar data do aniversário no ano atual
  let birthdayThisYear = new Date(currentYear, month - 1, day);
  
  // Se o aniversário já passou este ano, considerar o próximo ano
  if (birthdayThisYear < today) {
    birthdayThisYear = new Date(currentYear + 1, month - 1, day);
  }
  
  // Calcular diferença em milissegundos e converter para dias
  const diffTime = birthdayThisYear.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays;
}

/**
 * Retorna uma mensagem formatada sobre quantos dias faltam
 * @param days - Número de dias
 * @returns String formatada
 */
export function formatDaysUntil(days: number): string {
  if (days === 0) return "Hoje!";
  if (days === 1) return "Amanhã";
  if (days <= 7) return `${days} dias`;
  return `${days} dias`;
}

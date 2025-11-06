import { useEffect, useMemo, useRef, useState } from "react";
import { TodayBirthdays } from "./components/TodayBirthdays";
import { MonthBirthdays } from "./components/MonthBirthdays";
import type { Birthday } from "./data/birthdays";
import { fetchBirthdays } from "./services/api";
import { Settings } from "lucide-react";
import { Button } from "./components/ui/button";
import { Input } from "./components/ui/input";

export default function App() {
  const [today, setToday] = useState<Birthday[]>([]);
  const [month, setMonth] = useState<Birthday[]>([]);
  // Estados e refs para acesso administrativo via engrenagem
  const [showAdminPrompt, setShowAdminPrompt] = useState(false);
  const [adminPassword, setAdminPassword] = useState("");
  const [adminError, setAdminError] = useState("");
  const gearClicksRef = useRef(0);
  const gearResetTimeoutRef = useRef<number | null>(null);

  const handleGearClick = () => {
    // Reset timeout se já estiver setado
    if (gearResetTimeoutRef.current) {
      window.clearTimeout(gearResetTimeoutRef.current);
    }
    gearClicksRef.current += 1;
    if (gearClicksRef.current >= 3) {
      gearClicksRef.current = 0;
      setAdminError("");
      setAdminPassword("");
      setShowAdminPrompt(true);
      return;
    }
    // Necessário 3 cliques em até ~1.2s
    gearResetTimeoutRef.current = window.setTimeout(() => {
      gearClicksRef.current = 0;
    }, 1200);
  };

  const handleAdminSubmit = () => {
    if (adminPassword === "admin@admin") {
      // Navega para estatísticas (Apache)
      window.location.href = "http://localhost/aniversario/estatisticas.php";
    } else {
      setAdminError("Senha incorreta");
    }
  };

  useEffect(() => {
    let cancelled = false;
    (async () => {
      const [apiToday, apiMonth] = await Promise.all([
        fetchBirthdays("today"),
        fetchBirthdays("month"),
      ]);

      if (cancelled) return;
      // Sempre usar resultados da API, mesmo vazios (sem fallbacks antigos)
      setToday(apiToday);
      setMonth(apiMonth);
    })();
    return () => {
      cancelled = true;
    };
  }, []);

  const monthTitle = useMemo(() => {
    try {
      const name = new Date().toLocaleString("pt-BR", { month: "long" });
      return `Aniversariantes de ${name.charAt(0).toUpperCase() + name.slice(1)}`;
    } catch {
      return "Aniversariantes deste mês";
    }
  }, []);

  return (
    <div className="h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 flex items-center justify-center p-4 overflow-hidden">
      <div className="max-w-7xl w-full">
        {/* Single Container for All Information */}
        <div className="relative">
          <div className="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-blue-600/10 rounded-3xl blur-xl pointer-events-none z-0"></div>
          <div className="relative z-10 bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-3xl border border-slate-700/50 shadow-2xl overflow-hidden">
            <div className="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-400"></div>
            
            {/* Header */}
            <div className="text-center pt-4 pb-2 px-8">
              {/* Engrenagem e popover administrativo */}
              <button
                aria-label="Configurações"
                onClick={handleGearClick}
                className="absolute top-2 left-0 z-20 bg-slate-800/60 hover:bg-slate-700/70 border border-slate-700/50 rounded-md p-1.5 text-slate-200 transition-colors"
              >
                <Settings className="w-5 h-5" />
              </button>
              {showAdminPrompt && (
                <div className="absolute top-12 left-0 z-50 w-72 rounded-xl border border-slate-700/60 bg-gradient-to-br from-slate-800/90 to-slate-900/90 p-4 shadow-2xl">
                  <h3 className="text-base text-amber-300 mb-2">Acesso Administrativo</h3>
                  <p className="text-slate-400 text-xs mb-3">Insira a senha para abrir Estatísticas.</p>
                  <div className="space-y-2">
                    <Input
                      type="password"
                      placeholder="Senha"
                      value={adminPassword}
                      onChange={(e) => setAdminPassword(e.target.value)}
                      onKeyDown={(e) => {
                        if (e.key === "Enter") handleAdminSubmit();
                      }}
                      className="text-white placeholder:text-white/80"
                    />
                    {adminError && (
                      <div className="text-destructive text-xs">{adminError}</div>
                    )}
                    <div className="flex justify-end gap-2">
                      <Button variant="outline" size="sm" className="text-white" onClick={() => setShowAdminPrompt(false)}>Cancelar</Button>
                      <Button size="sm" className="text-white" onClick={handleAdminSubmit}>Entrar</Button>
                    </div>
                  </div>
                </div>
              )}
              {/* Título removido conforme solicitado */}
              {/* Texto movido para o rodapé */}
            </div>

            {/* Today's Birthdays Section */}
            <div className="px-8 pt-0 pb-4">
              <div className="flex items-center justify-center gap-2 mb-0">
                <div className="h-px w-8 bg-gradient-to-r from-transparent to-amber-400"></div>
                <h2 className="text-xl text-amber-300">
                  Aniversariantes de Hoje
                </h2>
                <div className="h-px w-8 bg-gradient-to-l from-transparent to-amber-400"></div>
              </div>
              
              <div className="h-[300px] sm:h-[280px] md:h-[300px] lg:h-[320px] xl:h-[360px] -mt-2">
                <TodayBirthdays birthdays={today} />
              </div>
            </div>

            {/* Separator */}
            <div className="px-8">
              <div className="h-px bg-gradient-to-r from-transparent via-slate-600 to-transparent"></div>
            </div>

            {/* Month's Birthdays Section */}
            <div className="px-8 py-4">
              <div className="flex items-center justify-center gap-2 mb-3">
                <div className="h-px w-8 bg-gradient-to-r from-transparent to-cyan-400"></div>
                <h2 className="text-xl text-cyan-300">
                  {monthTitle}
                </h2>
                <div className="h-px w-8 bg-gradient-to-l from-transparent to-cyan-400"></div>
              </div>
              
              <div className="py-2">
                <MonthBirthdays birthdays={month} />
              </div>
            </div>

            {/* Footer */}
            <div className="pb-4 px-8">
              <div className="flex flex-wrap items-center justify-center gap-3 text-xs">
                <p className="text-slate-500">Desejamos muita felicidade e sucesso! 🎉</p>
                <p className="text-slate-500">Celebrando momentos especiais da nossa equipe</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      {/* Popover renderizado no cabeçalho acima */}
    </div>
  );
}

import { motion } from "motion/react";
import { Birthday } from "../data/birthdays";
import { Calendar } from "lucide-react";
import { daysUntilBirthday, formatDaysUntil } from "../utils/dateUtils";

interface MonthBirthdaysProps {
  birthdays: Birthday[];
}

export function MonthBirthdays({ birthdays }: MonthBirthdaysProps) {
  // Duplicar os aniversariantes para criar efeito de loop infinito
  const duplicatedBirthdays = [...birthdays, ...birthdays];

  return (
    <div className="relative overflow-hidden">
      <motion.div
        className="flex gap-4"
        animate={{
          x: [0, -128 * birthdays.length],
        }}
        transition={{
          x: {
            duration: birthdays.length * 5,
            repeat: Infinity,
            ease: "linear",
          },
        }}
      >
        {duplicatedBirthdays.map((birthday, index) => {
          const daysLeft = daysUntilBirthday(birthday.date);
          const daysText = formatDaysUntil(daysLeft);
          
          return (
            <div
              key={`${birthday.id}-${index}`}
              className="flex-shrink-0 w-28"
            >
              <div className="relative group">
                <div className="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg blur opacity-25 group-hover:opacity-75 transition duration-300"></div>
                <div className="relative bg-slate-800/50 backdrop-blur-sm rounded-lg overflow-hidden border border-slate-700/50 hover:border-cyan-500/50 transition-all duration-300">
                  <div className="aspect-square overflow-hidden">
                    <img
                      src={birthday.photo}
                      alt={birthday.name}
                      className="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                    />
                  </div>
                  <div className="p-1.5 bg-gradient-to-t from-slate-900 to-transparent">
                    <h4 className="text-white text-xs truncate mb-0.5">
                      {birthday.name}
                    </h4>
                    <div className="flex items-center gap-0.5 text-cyan-400 text-xs mb-0.5">
                      <Calendar className="w-2.5 h-2.5" />
                      <span className="text-[10px]">{birthday.date}</span>
                    </div>
                    <div className="text-[9px] text-cyan-300/70 truncate">
                      {daysText}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </motion.div>
    </div>
  );
}

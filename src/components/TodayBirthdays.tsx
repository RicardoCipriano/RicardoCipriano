import { motion, AnimatePresence } from "motion/react";
import { useEffect, useState } from "react";
import { Birthday } from "../data/birthdays";
import { Cake } from "lucide-react";

interface TodayBirthdaysProps {
  birthdays: Birthday[];
}

export function TodayBirthdays({ birthdays }: TodayBirthdaysProps) {
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (birthdays.length === 0) return;

    const interval = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % birthdays.length);
    }, 4000);

    return () => clearInterval(interval);
  }, [birthdays.length]);

  if (birthdays.length === 0) {
    return (
      <div className="text-center text-white/60 py-12">
        Nenhum aniversariante hoje
      </div>
    );
  }

  const currentBirthday = birthdays[currentIndex];

  return (
    <div className="relative h-full flex items-center justify-center overflow-hidden py-2">
      <AnimatePresence mode="wait">
        <motion.div
          key={currentBirthday.id}
          initial={{ opacity: 0, scale: 0.8 }}
          animate={{ opacity: 1, scale: 1 }}
          exit={{ opacity: 0, scale: 0.8 }}
          transition={{ duration: 0.6, ease: "easeInOut" }}
          className="flex flex-col items-center justify-center gap-3 w-full"
        >
          <motion.div
            initial={{ scale: 0.8 }}
            animate={{ scale: [0.8, 1.05, 1] }}
            transition={{ 
              duration: 0.8,
              ease: "easeOut",
              times: [0, 0.6, 1]
            }}
            className="relative flex items-center justify-center"
          >
            {/* Horizontal animated lines */}
            <motion.div
              animate={{ x: [-100, 100] }}
              transition={{ duration: 3, repeat: Infinity, ease: "linear", repeatType: "reverse" }}
              className="absolute -left-16 -right-16 top-1/4 h-0.5 bg-gradient-to-r from-transparent via-amber-400/60 to-transparent"
            ></motion.div>
            <motion.div
              animate={{ x: [100, -100] }}
              transition={{ duration: 4, repeat: Infinity, ease: "linear", repeatType: "reverse" }}
              className="absolute -left-16 -right-16 top-1/2 h-0.5 bg-gradient-to-r from-transparent via-yellow-300/40 to-transparent"
            ></motion.div>
            <motion.div
              animate={{ x: [-100, 100] }}
              transition={{ duration: 3.5, repeat: Infinity, ease: "linear", repeatType: "reverse" }}
              className="absolute -left-16 -right-16 bottom-1/4 h-0.5 bg-gradient-to-r from-transparent via-amber-400/50 to-transparent"
            ></motion.div>
            
            {/* Horizontal particle effects */}
            <motion.div
              animate={{ 
                x: [-80, 80],
                opacity: [0, 1, 0]
              }}
              transition={{ duration: 2.5, repeat: Infinity, ease: "easeInOut" }}
              className="absolute left-0 top-1/3 w-2 h-2 bg-amber-300 rounded-full blur-sm"
            ></motion.div>
            <motion.div
              animate={{ 
                x: [80, -80],
                opacity: [0, 1, 0]
              }}
              transition={{ duration: 3, delay: 0.5, repeat: Infinity, ease: "easeInOut" }}
              className="absolute right-0 top-2/3 w-1.5 h-1.5 bg-yellow-300 rounded-full blur-sm"
            ></motion.div>

            <div className="relative w-32 h-32 rounded-full overflow-hidden border-4 border-amber-400 shadow-2xl ring-4 ring-amber-400/20">
              <img
                src={currentBirthday.photo}
                alt={currentBirthday.name}
                className="w-full h-full object-cover"
              />
            </div>
            <div className="absolute -top-2 -right-2 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-full p-2 shadow-xl">
              <Cake className="w-5 h-5 text-slate-900" />
            </div>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3, duration: 0.5 }}
            className="text-center max-w-md"
          >
            <h3 className="text-2xl text-white mb-1">
              {currentBirthday.name}
            </h3>
            <p className="text-amber-300">
              {currentBirthday.department}
            </p>
            <div className="mt-1 text-amber-200/60 text-sm">
              🎂 {currentBirthday.date}
            </div>
          </motion.div>
        </motion.div>
      </AnimatePresence>

      <div className="absolute bottom-0 left-1/2 -translate-x-1/2 flex gap-2">
        {birthdays.map((_, index) => (
          <div
            key={index}
            className={`h-1.5 rounded-full transition-all duration-300 ${
              index === currentIndex
                ? "w-8 bg-amber-400"
                : "w-1.5 bg-white/30"
            }`}
          />
        ))}
      </div>
    </div>
  );
}

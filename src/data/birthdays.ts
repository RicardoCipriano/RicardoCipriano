export interface Birthday {
  id: number;
  name: string;
  photo: string;
  date: string;
  department: string;
}

export const todayBirthdays: Birthday[] = [
  {
    id: 1,
    name: "Maria Silva",
    photo: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop",
    date: "05/11",
    department: "Marketing"
  },
  {
    id: 2,
    name: "João Santos",
    photo: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop",
    date: "05/11",
    department: "Vendas"
  },
  {
    id: 3,
    name: "Ana Costa",
    photo: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop",
    date: "05/11",
    department: "TI"
  }
];

export const monthBirthdays: Birthday[] = [
  {
    id: 4,
    name: "Carlos Oliveira",
    photo: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop",
    date: "08/11",
    department: "Financeiro"
  },
  {
    id: 5,
    name: "Juliana Pereira",
    photo: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&fit=crop",
    date: "12/11",
    department: "RH"
  },
  {
    id: 6,
    name: "Pedro Almeida",
    photo: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop",
    date: "15/11",
    department: "Operações"
  },
  {
    id: 7,
    name: "Fernanda Lima",
    photo: "https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?w=400&h=400&fit=crop",
    date: "18/11",
    department: "Marketing"
  },
  {
    id: 8,
    name: "Roberto Souza",
    photo: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop",
    date: "22/11",
    department: "Vendas"
  },
  {
    id: 9,
    name: "Camila Rodrigues",
    photo: "https://images.unsplash.com/photo-1517841905240-472988babdf9?w=400&h=400&fit=crop",
    date: "25/11",
    department: "TI"
  },
  {
    id: 10,
    name: "Lucas Martins",
    photo: "https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=400&h=400&fit=crop",
    date: "28/11",
    department: "Financeiro"
  }
];

import { SelectItem } from "@/types";

export function useYears() {
  const startYear = 2009;
  const currentYear = new Date().getFullYear();

  const years: SelectItem[] = [
    { id: 0, name: "Select Year" },

    ...Array.from({ length: currentYear - startYear + 2 }, (_, i) => {
      const year = currentYear - i;
      return { id: year, name: year.toString() };
    }),
  ];

  return { years };
}

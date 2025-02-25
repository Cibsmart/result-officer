import { SelectItem } from "@/types";

export function useYears(startYear: number = 1999, endYear: number = new Date().getFullYear()) {
  const years: SelectItem[] = [
    { id: 0, name: "Select Year" },

    ...Array.from({ length: endYear - startYear + 2 }, (_, i) => {
      const year = endYear - i;
      return { id: year, name: year.toString() };
    }),
  ];

  return { years };
}

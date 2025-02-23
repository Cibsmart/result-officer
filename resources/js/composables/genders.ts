import Gender = App.Enums.Gender;
import { EnumSelectItem } from "@/types";

export function useGenders() {
  const genderMap: Record<Gender, string> = {
    M: "MALE",
    F: "FEMALE",
    U: "UNKNOWN",
  };

  const genders: EnumSelectItem[] = (["0", "M", "F"] as Gender[]).map((gender) => ({
    id: gender,
    name: genderMap[gender] ?? "Select Gender",
  }));

  return { genders };
}

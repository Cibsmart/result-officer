import LevelEnum = App.Enums.LevelEnum;
import { EnumSelectItem } from "@/types";

export function useLevels() {
  const levelMap: Record<LevelEnum, string> = {
    100: "100",
    200: "200",
    300: "300",
    400: "400",
    500: "500",
    600: "600",
  };

  const levels: EnumSelectItem[] = (["0", ...Object.values(levelMap)] as LevelEnum[]).map((level) => ({
    id: level,
    name: levelMap[level] ?? "Select Level",
  }));

  return { levels };
}

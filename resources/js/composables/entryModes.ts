import EntryMode = App.Enums.EntryMode;
import { EnumSelectItem } from "@/types";

export function useEntryModes() {
  const entryModeMap: Record<EntryMode, string> = {
    UTME: "UTME",
    DENT: "DIRECT ENTRY",
    PD: "PRE-DEGREE",
    TRAN: "TRANSFER",
  };

  const modes: EnumSelectItem[] = (["0", "UTME", "DENT", "PD", "TRAN"] as EntryMode[]).map((mode) => ({
    id: mode,
    name: entryModeMap[mode] ?? "Select Entry Mode",
  }));

  return { modes };
}

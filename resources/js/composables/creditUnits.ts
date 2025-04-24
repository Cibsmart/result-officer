import CreditUnit = App.Enums.CreditUnit;
import { SelectItem } from '@/types';

export function useCreditUnits() {
    const unitMap: Record<CreditUnit, string> = {
        0: '0',
        1: '1',
        2: '2',
        3: '3',
        4: '4',
        5: '5',
        6: '6',
        7: '7',
        8: '8',
        9: '9',
        10: '10',
        11: '11',
        12: '12',
        15: '15',
        16: '16',
        18: '18',
        24: '24',
        30: '30',
    };

    const unitList = [0, 1, 2, 3, 4, 5, 6, 7, 8, 12, 15, 16, 18];

    const units: SelectItem[] = (unitList as CreditUnit[]).map((unit) => ({
        id: unit,
        name: unitMap[unit] ?? 'Select Credit Unit',
    }));

    return { units };
}

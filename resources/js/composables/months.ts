import Month = App.Enums.Months;
import { EnumSelectItem } from '@/types';

export function useMonths() {
    const monthsMap: Record<Month, string> = {
        January: 'JANUARY',
        February: 'FEBRUARY',
        March: 'MARCH',
        April: 'APRIL',
        May: 'MAY',
        June: 'JUNE',
        July: 'JULY',
        August: 'AUGUST',
        September: 'SEPTEMBER',
        October: 'OCTOBER',
        November: 'NOVEMBER',
        December: 'DECEMBER',
    };

    const monthList = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];

    const months: EnumSelectItem[] = ([0, ...monthList] as Month[]).map((month) => ({
        id: month,
        name: monthsMap[month] ?? 'Select Month',
    }));

    return { months };
}

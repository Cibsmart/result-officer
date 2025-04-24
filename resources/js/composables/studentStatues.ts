import Status = App.Enums.StudentStatus;
import { EnumSelectItem } from '@/types';

export function useStudentStatues() {
    const statusMap: Record<Status, string> = {
        new: 'NEW',
        active: 'ACTIVE',
        inactive: 'INACTIVE',
        probation: 'PROBATION',
        withdrawn: 'WITHDRAWN',
        expelled: 'EXPELLED',
        suspended: 'SUSPENDED',
        deceased: 'DECEASED',
        unknown: 'UNKNOWN',
        transferred: 'TRANSFERRED',
        final: 'FINAL',
        extra: 'EXTRA',
        cleared: 'CLEARED',
        graduated: 'GRADUATED',
    };

    const statusList = [
        'new',
        'active',
        'inactive',
        'probation',
        'withdrawn',
        'expelled',
        'suspended',
        'deceased',
        'unknown',
        'transferred',
        'final',
        'extra',
        'cleared',
        'graduated',
    ];

    const statues: EnumSelectItem[] = (['0', ...statusList] as Status[]).map((status) => ({
        id: status,
        name: statusMap[status] ?? 'Select Status',
    }));

    return { statues };
}

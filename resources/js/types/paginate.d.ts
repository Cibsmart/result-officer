export interface PaginationInterface {
    currentPage: number;
    data: Array<any>;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<PaginationLink>;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string;
    to: number;
    total: number;
}

export type PaginationLink = {
    active: boolean;
    label: string;
    url: string;
};

export interface PaginatedStudentListData extends PaginationInterface {
    data: Array<App.Data.Students.StudentBasicData>;
}

export interface PaginatedGraduandListData extends PaginationInterface {
    data: Array<App.Data.Graduands.GraduandData>;
}

export interface PaginatedVettingEventGroupListData extends PaginationInterface {
    data: Array<App.Data.Vetting.VettingEventGroupData>;
}

export type PaginatedData = PaginatedStudentListData | PaginatedGraduandListData | PaginatedVettingEventGroupListData;

declare namespace App.Data.Results {
  export type ResultData = {
    id: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: number;
    totalScore: number;
    grade: string;
    gradePoint: number;
    remark: string | null;
  };
  export type SemesterResultData = {
    id: number;
    results: Array<App.Data.Results.ResultData>;
    semester: string;
    creditUnitTotal: number;
    gradePointTotal: number;
    gradePointAverage: number;
  };
  export type SessionResultData = {
    id: number;
    semesterResults: Array<App.Data.Results.SemesterResultData>;
    session: string;
    cumulativeGradePointAverage: number;
  };
  export type StudentResultData = {
    id: number;
    enrollments: Array<App.Data.Results.SessionResultData>;
    finalCumulativeGradePointAverage: number;
  };
}
declare namespace App.Data.Shared {
  export type SharedData = {
    user: App.Data.Shared.UserData;
  };
  export type UserData = {
    name: string;
    email: string;
  };
}
declare namespace App.Data.Students {
  export type StudentData = {
    matriculationNumber: string;
    lastName: string;
    firstName: string;
    otherNames: string | null;
    gender: App.Enums.GenderEnum;
    birthDate: string;
    program: string;
    department: string;
    faculty: string;
    admissionYear: string;
  };
}
declare namespace App.Enums {
  export type CourseStatusEnum = "F" | "R";
  export type CreditUnitEnum = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 12 | 15 | 18;
  export type GenderEnum = "M" | "F";
  export type Grade = 5 | 4 | 3 | 2 | 1 | 0;
  export type LevelEnum = 100 | 200 | 300 | 400 | 500 | 600;
  export type ResultRemark = "PAS" | "FAL" | "ABS" | "MAL";
  export type RoleEnum = "super-admin" | "admin" | "desk-officer" | "exam-officer" | "database-officer" | "user";
  export type ScoreTypeEnum = "course-work" | "exam";
  export type StudentStatusEnum =
    | "new"
    | "active"
    | "probation"
    | "withdrawn"
    | "expelled"
    | "suspended"
    | "deceased"
    | "transferred"
    | "graduated";
}
declare namespace App.ViewModels.Results {
  export type ResultViewPage = {
    student: App.Data.Students.StudentData;
    results: App.Data.Results.StudentResultData;
  };
}

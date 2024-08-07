declare namespace App.Data.Department {
  export type DepartmentData = {
    id: number;
    name: string;
  };
  export type DepartmentListData = {
    departments: { [key: number]: App.Data.Department.DepartmentData } | Array<any>;
  };
}
declare namespace App.Data.Level {
  export type LevelData = {
    id: number;
    name: App.Enums.LevelEnum | string;
  };
  export type LevelListData = {
    levels: { [key: number]: App.Data.Level.LevelData } | Array<any>;
  };
}
declare namespace App.Data.Results {
  export type GradingSchemeData = {
    range: string;
    interpretation: string;
    grade: string;
    gradePoint: number;
  };
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
    results: { [key: number]: App.Data.Results.ResultData } | Array<any>;
    semester: string;
    creditUnitTotal: number;
    gradePointTotal: number;
    gradePointAverage: number;
  };
  export type SessionResultData = {
    id: number;
    semesterResults: { [key: number]: App.Data.Results.SemesterResultData } | Array<any>;
    session: string;
    year: string;
    cumulativeGradePointAverage: number;
  };
  export type StudentResultData = {
    id: number;
    enrollments: { [key: number]: App.Data.Results.SessionResultData } | Array<any>;
    finalCumulativeGradePointAverage: number;
    degreeClass: string;
    degreeAwarded: string;
    graduationYear: number;
  };
}
declare namespace App.Data.Session {
  export type SessionData = {
    id: number;
    name: string;
  };
  export type SessionListData = {
    sessions: { [key: number]: App.Data.Session.SessionData } | Array<any>;
  };
}
declare namespace App.Data.Students {
  export type StudentData = {
    id: number;
    registrationNumber: string;
    lastName: string;
    firstName: string;
    otherNames: string | null;
    name: string;
    gender: App.Enums.GenderEnum;
    birthDate: string;
    program: string;
    department: string;
    faculty: string;
    admissionYear: number;
    nationality: string;
  };
}
declare namespace App.Data.Summary {
  export type DepartmentResultSummaryData = {
    department: App.Data.Department.DepartmentData;
    session: App.Data.Session.SessionData;
    level: App.Data.Level.LevelData;
    students: { [key: number]: App.Data.Summary.StudentResultSummaryData } | Array<any>;
  };
  export type StudentResultSummaryData = {
    student: App.Data.Students.StudentData;
    fcgpa: number;
  };
}
declare namespace App.Enums {
  export type CourseStatusEnum = "F" | "R";
  export type CreditUnitEnum = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 12 | 15 | 18;
  export type DegreeClassEnum =
    | "FIRST CLASS HONOURS"
    | "SECOND CLASS HONOURS (UPPER DIVISION)"
    | "SECOND CLASS HONOURS (LOWER DIVISION)"
    | "THIRD CLASS HONOURS"
    | "PASS"
    | "FAIL";
  export type GenderEnum = "M" | "F";
  export type Grade = 5 | 4 | 3 | 2 | 1 | 0;
  export type LevelEnum = 100 | 200 | 300 | 400 | 500 | 600;
  export type RecordSource = "portal" | "excel" | "legacy";
  export type ResultRemark = "PAS" | "FAL" | "ABS" | "MAL";
  export type RoleEnum = "super-admin" | "admin" | "desk-officer" | "exam-officer" | "database-officer" | "user";
  export type ScoreTypeEnum = "course-work" | "exam";
  export type StudentStatusEnum =
    | "new"
    | "active"
    | "inactive"
    | "probation"
    | "withdrawn"
    | "expelled"
    | "suspended"
    | "deceased"
    | "transferred"
    | "graduated";
}

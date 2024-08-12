declare namespace App.Data.Department {
  export type DepartmentData = {
    id: number;
    name: string;
  };
  export type DepartmentListData = {
    departments: Array<App.Data.Department.DepartmentData>;
  };
}
declare namespace App.Data.Download {
  export type PortalCourseData = {
    onlineId: string;
    code: string;
    title: string;
  };
  export type PortalDateData = {
    day: string;
    month: string;
    year: string;
  };
  export type PortalDepartmentData = {
    onlineId: string;
    departmentCode: string;
    departmentName: string;
    facultyName: string;
    programs: Array<App.Data.Download.PortalProgramData>;
  };
  export type PortalProgramData = {
    id: string;
    name: string;
  };
  export type PortalStudentData = {
    onlineId: string;
    lastName: string;
    firstName: string;
    otherNames: string;
    registrationNumber: string;
    gender: string;
    dateOfBirth: App.Data.Download.PortalDateData;
    departmentId: string;
    option: string;
    state: string;
    localGovernment: string;
    entrySession: string;
    entryMode: string;
    entryLevel: string;
    jambRegistrationNumber: string;
    email: string;
    phoneNumber: string;
  };
}
declare namespace App.Data.Level {
  export type LevelData = {
    id: number;
    name: App.Enums.LevelEnum | string;
  };
  export type LevelListData = {
    levels: Array<App.Data.Level.LevelData>;
  };
}
declare namespace App.Data.Response {
  export type ResponseData = {
    key: string;
    message: string;
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
    year: string;
    cumulativeGradePointAverage: number;
  };
  export type StudentResultData = {
    id: number;
    enrollments: Array<App.Data.Results.SessionResultData>;
    finalCumulativeGradePointAverage: number;
    degreeClass: string;
    degreeAwarded: string;
    graduationYear: number;
  };
  export type TranscriptData = {
    recordsUnitHead: string;
    gradingSchemes: Array<App.Data.Results.GradingSchemeData>;
  };
}
declare namespace App.Data.Session {
  export type SessionData = {
    id: number;
    name: string;
  };
  export type SessionListData = {
    sessions: Array<App.Data.Session.SessionData>;
  };
  export type SessionTestData = {
    session: Array<any>;
  };
}
declare namespace App.Data.Shared {
  export type NotificationData = {
    type: App.Enums.NotificationType;
    body: string;
  };
  export type SharedData = {
    user: App.Data.Shared.UserData;
    notification: App.Data.Shared.NotificationData | null;
  };
  export type UserData = {
    id: number;
    name: string;
    email: string;
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
    students: Array<App.Data.Summary.StudentResultSummaryData>;
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
  export type NotificationType = "success" | "error" | "warning" | "info";
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
declare namespace App.ViewModels.Downloads {
  export type DownloadStudentPage = {
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
  };
}
declare namespace App.ViewModels.Results {
  export type ResultViewPage = {
    student: App.Data.Students.StudentData;
    results: App.Data.Results.StudentResultData;
  };
}
declare namespace App.ViewModels.Summary {
  export type SummaryFormPage = {
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
    level: App.Data.Level.LevelListData;
  };
  export type SummaryViewPage = {
    department: App.Data.Summary.DepartmentResultSummaryData;
  };
}

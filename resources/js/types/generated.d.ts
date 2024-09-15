declare namespace App.Data.Composite {
  export type CompositeCourseData = {
    code: string;
    grade: string;
    score: string;
  };
  export type CompositeCourseListData = {
    code: string;
    unit: number;
  };
  export type CompositeRowData = {
    studentId: number;
    studentName: string;
    registrationNumber: string;
    creditUnitTotal: string;
    gradePointTotal: string;
    gradePointAverage: string;
    levelCourses: Array<App.Data.Composite.CompositeCourseData>;
    otherCourses: string;
    remark: string;
  };
  export type CompositeSheetData = {
    program: App.Data.Program.ProgramData;
    faculty: App.Data.Faculty.FacultyData;
    session: App.Data.Session.SessionData;
    semester: App.Data.Semester.SemesterData;
    level: App.Data.Level.LevelData;
    courses: Array<App.Data.Composite.CompositeCourseListData>;
    students: Array<App.Data.Composite.CompositeRowData>;
    hasOtherCourses: boolean;
  };
}
declare namespace App.Data.Course {
  export type CourseData = {
    id: number;
    name: string;
  };
  export type CourseListData = {
    courses: Array<App.Data.Course.CourseData>;
  };
}
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
  export type PortalCourseRegistrationData = {
    onlineId: string;
    registrationNumber: string;
    session: string;
    semester: string;
    level: string;
    courseId: string;
    creditUnit: string;
    registrationDate: App.Data.Download.PortalDateData;
    source: App.Enums.RecordSource;
  };
  export type PortalDateData = {
    value: string | null;
  };
  export type PortalDepartmentData = {
    onlineId: string;
    departmentCode: string;
    departmentName: string;
    facultyName: string;
    programs: Array<App.Data.Download.PortalProgramData>;
  };
  export type PortalProgramData = {
    name: string;
  };
  export type PortalResultData = {
    onlineId: string;
    courseRegistrationId: string;
    registrationNumber: string;
    inCourseScore: string;
    examScore: string;
    totalScore: string;
    grade: string;
    uploadDate: App.Data.Download.PortalDateData;
    source: App.Enums.RecordSource;
    examDate: string;
    lecturerName: string;
    lecturerDepartment: string;
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
declare namespace App.Data.Faculty {
  export type FacultyData = {
    id: number;
    name: string;
  };
}
declare namespace App.Data.Import {
  export type ImportEventData = {
    target: string;
    type: App.Enums.ImportEventType;
    content: string;
    count: number;
    status: App.Enums.ImportEventStatus;
    date: string;
    completed: boolean;
  };
  export type PendingImportEventData = {
    content: string;
    date: string;
    width: number;
    elements: { [key: number]: App.Enums.ImportEventStatus };
  };
}
declare namespace App.Data.Level {
  export type LevelData = {
    id: number;
    name: string;
  };
  export type LevelListData = {
    levels: Array<App.Data.Level.LevelData>;
  };
}
declare namespace App.Data.Program {
  export type ProgramData = {
    id: number;
    name: string;
  };
  export type ProgramListData = {
    programs: Array<App.Data.Program.ProgramData>;
  };
}
declare namespace App.Data.Response {
  export type ResponseData = {
    key: string;
    message: string | boolean;
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
    formattedCreditUnitTotal: string;
    formattedGradePointTotal: string;
    formattedGPA: string;
  };
  export type SessionResultData = {
    id: number;
    semesterResults: Array<App.Data.Results.SemesterResultData>;
    session: string;
    year: string;
    cumulativeGradePointAverage: number;
    formattedCGPA: string;
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
declare namespace App.Data.Semester {
  export type SemesterData = {
    id: number;
    name: string;
  };
  export type SemesterListData = {
    semesters: Array<App.Data.Semester.SemesterData>;
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
    gender: string;
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
  export type ClassOfDegree =
    | "FIRST CLASS HONOURS"
    | "SECOND CLASS HONOURS (UPPER DIVISION)"
    | "SECOND CLASS HONOURS (LOWER DIVISION)"
    | "THIRD CLASS HONOURS"
    | "PASS"
    | "FAIL";
  export type CourseStatusEnum = "F" | "R";
  export type CreditUnitEnum = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 12 | 15 | 18;
  export type GenderEnum = "M" | "F";
  export type Grade = "A" | "B" | "C" | "D" | "E" | "F";
  export type ImportEventStatus =
    | "new"
    | "started"
    | "downloading"
    | "downloaded"
    | "saving"
    | "processing"
    | "failed"
    | "completed";
  export type ImportEventType = "results" | "courses" | "departments" | "students" | "registrations";
  export type LevelEnum = "100" | "200" | "300" | "400" | "500" | "600";
  export type NotificationType = "success" | "error" | "warning" | "info";
  export type RawDataStatus = "pending" | "processed";
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
  export type YearEnum = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8;
}
declare namespace App.ViewModels.Downloads {
  export type DownloadCourseRegistrationPage = {
    departments: App.Data.Department.DepartmentListData;
    sessions: App.Data.Session.SessionListData;
    semesters: App.Data.Semester.SemesterListData;
    courses: App.Data.Course.CourseListData;
    levels: App.Data.Level.LevelListData;
  };
  export type DownloadCoursesPage = {
    events: any;
    pending: App.Data.Import.PendingImportEventData;
  };
  export type DownloadStudentPage = {
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
  };
}
declare namespace App.ViewModels.Reports {
  export type CompositeFormPage = {
    program: App.Data.Program.ProgramListData;
    semester: App.Data.Semester.SemesterListData;
    session: App.Data.Session.SessionListData;
    level: App.Data.Level.LevelListData;
  };
  export type CompositeViewPage = {
    data: App.Data.Composite.CompositeSheetData;
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

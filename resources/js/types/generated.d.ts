declare namespace App.Data.Clearance {
  export type ClearanceMonthData = {
    id: number;
    name: string;
  };
  export type ClearanceMonthListData = {
    months: Array<App.Data.Clearance.ClearanceMonthData>;
  };
  export type ClearanceYearData = {
    id: number;
    name: string;
  };
  export type ClearanceYearListData = {
    years: Array<App.Data.Clearance.ClearanceYearData>;
  };
}
declare namespace App.Data.Cleared {
  export type ClearedStudentData = {
    id: number;
    name: string;
    registrationNumber: string;
    gender: App.Enums.Gender;
    status: App.Enums.StudentStatus;
    fcgpa: string;
    slug: string;
    batch: string;
    numberOfResults: number;
  };
  export type ClearedStudentListData = {
    faculty: App.Data.Faculty.FacultyData;
    department: App.Data.Department.DepartmentData;
    data: Array<App.Data.Cleared.ClearedStudentData>;
  };
}
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
    slug: string;
  };
  export type CourseListData = {
    courses: Array<App.Data.Course.CourseData>;
  };
}
declare namespace App.Data.Department {
  export type DepartmentData = {
    id: number;
    name: string;
    slug: string;
    programs: App.Data.Program.ProgramListData | null;
  };
  export type DepartmentInfoData = {
    faculty: App.Data.Faculty.FacultyData;
    department: App.Data.Department.DepartmentData;
  };
  export type DepartmentListData = {
    data: Array<App.Data.Department.DepartmentData>;
  };
}
declare namespace App.Data.Download {
  export type PortalCourseData = {
    onlineId: string;
    code: string;
    title: string;
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
  export type PortalRegistrationData = {
    onlineId: string;
    registrationNumber: string;
    session: string;
    semester: string;
    level: string;
    courseId: string;
    creditUnit: string;
    registrationDate: string | null;
    source: App.Enums.RecordSource;
  };
  export type PortalResultData = {
    onlineId: string;
    courseRegistrationId: string;
    registrationNumber: string;
    inCourseScore: string;
    examScore: string;
    totalScore: string;
    grade: string;
    uploadDate: string;
    source: App.Enums.RecordSource;
    examDate: string;
    lecturerName: string;
    lecturerEmail: string;
    lecturerPhoneNumber: string;
    lecturerDepartment: string;
  };
  export type PortalStudentData = {
    onlineId: string;
    lastName: string;
    firstName: string;
    otherNames: string;
    registrationNumber: string;
    gender: string;
    dateOfBirth: string;
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
declare namespace App.Data.Enums {
  export type CreditUnitData = {
    id: number;
    name: string;
  };
  export type CreditUnitListData = {
    data: Array<App.Data.Enums.CreditUnitData>;
  };
  export type StudentStatusData = {
    id: string;
    name: string;
  };
  export type StudentStatusListData = {
    data: Array<App.Data.Enums.StudentStatusData>;
  };
}
declare namespace App.Data.ExamOfficer {
  export type ExamOfficerData = {
    id: number;
    name: string;
  };
  export type ExamOfficerListData = {
    officers: Array<App.Data.ExamOfficer.ExamOfficerData>;
  };
}
declare namespace App.Data.Faculty {
  export type FacultyData = {
    id: number;
    name: string;
    slug: string;
  };
}
declare namespace App.Data.FinalResults {
  export type FinalResultData = {
    id: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: number;
    totalScore: number;
    grade: string;
    gradePoint: number;
    remark: string | null;
    dateUpdated: string;
  };
  export type FinalSemesterResultData = {
    id: number;
    results: Array<App.Data.FinalResults.FinalResultData>;
    semester: string;
    creditUnitTotal: number;
    gradePointTotal: number;
    gradePointAverage: number;
    formattedCreditUnitTotal: string;
    formattedGradePointTotal: string;
    formattedGPA: string;
    resultsCount: number;
  };
  export type FinalSessionResultData = {
    id: number;
    finalSemesterResults: Array<App.Data.FinalResults.FinalSemesterResultData>;
    session: string;
    year: string;
    cumulativeGradePointAverage: number;
    formattedCGPA: string;
    resultsCount: number;
  };
  export type FinalStudentResultData = {
    id: number;
    finalSessionEnrollments: Array<App.Data.FinalResults.FinalSessionResultData>;
    finalCumulativeGradePointAverage: number;
    formattedFCGPA: string;
    degreeClass: string;
    degreeAwarded: string;
    graduationYear: number;
    resultsCount: number;
  };
  export type FinalTranscriptData = {
    recordsUnitHead: string;
    gradingSchemes: Array<App.Data.Grading.GradingSchemeData>;
  };
}
declare namespace App.Data.Grading {
  export type GradingSchemeData = {
    range: string;
    interpretation: string;
    grade: string;
    gradePoint: number;
  };
}
declare namespace App.Data.Imports {
  export type ExcelImportEventData = {
    id: number;
    userId: number;
    fileName: string;
    type: App.Enums.ExcelImportType;
    status: App.Enums.ImportEventStatus;
    statusColor: App.Enums.StatusColor;
    message: string | null;
  };
  export type ExcelImportEventListData = {
    events: Array<App.Data.Imports.ExcelImportEventData>;
  };
  export type ImportEventData = {
    id: number;
    target: string;
    type: App.Enums.ImportEventType;
    content: string;
    description: string;
    status: App.Enums.ImportEventStatus;
    date: string;
  };
  export type PendingImportEventData = {
    id: number;
    type: App.Enums.ImportEventType;
    content: string;
    date: string;
    width: number;
    elements: { [key: number]: App.Enums.ImportEventStatus };
    status: App.Enums.ImportEventStatus;
    canBeContinued: boolean;
  };
}
declare namespace App.Data.Level {
  export type LevelData = {
    id: number;
    name: string;
    slug: string;
  };
  export type LevelListData = {
    levels: Array<App.Data.Level.LevelData>;
  };
}
declare namespace App.Data.Program {
  export type ProgramData = {
    id: number;
    name: string;
    slug: string;
  };
  export type ProgramInfoData = {
    programName: string;
    departmentName: string;
    facultyName: string;
  };
  export type ProgramListData = {
    programs: Array<App.Data.Program.ProgramData>;
  };
}
declare namespace App.Data.Query {
  export type ProgramCoursesData = {
    curriculumId: number;
    programCourseId: number;
    sessionId: number;
    session: string;
    levelId: number;
    level: string;
    semester: string;
    courseId: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: App.Enums.CreditUnit;
    courseType: App.Enums.CourseType;
    minElectiveUnit: number;
    minElectiveCount: number;
  };
  export type StudentCoursesData = {
    studentId: number;
    registrationId: number;
    sessionId: number;
    session: string;
    semester: string;
    courseId: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: App.Enums.CreditUnit;
    courseStatus: App.Enums.CourseStatus;
    totalScore: number | null;
    grade: App.Enums.Grade | null;
    gradePoint: number | null;
    programCurriculumCourseId: number | null;
  };
}
declare namespace App.Data.Response {
  export type ResponseData = {
    key: string;
    message: string | boolean;
  };
}
declare namespace App.Data.Results {
  export type ResultData = {
    id: number;
    resultId: number;
    courseCode: string;
    courseTitle: string;
    creditUnit: number;
    totalScore: number;
    grade: string;
    gradePoint: number;
    remark: string | null;
    dateUpdated: string;
    inCourseScore: number;
    examScore: number;
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
    resultsCount: number;
  };
  export type SessionResultData = {
    id: number;
    semesterResults: Array<App.Data.Results.SemesterResultData>;
    session: string;
    year: string;
    cumulativeGradePointAverage: number;
    formattedCGPA: string;
    resultsCount: number;
  };
  export type StudentResultData = {
    id: number;
    sessionEnrollments: Array<App.Data.Results.SessionResultData>;
    finalCumulativeGradePointAverage: number;
    formattedFCGPA: string;
    degreeClass: string;
    degreeAwarded: string;
    graduationYear: number;
    resultsCount: number;
  };
  export type TranscriptData = {
    recordsUnitHead: string;
    gradingSchemes: Array<App.Data.Grading.GradingSchemeData>;
  };
}
declare namespace App.Data.Semester {
  export type SemesterData = {
    id: number;
    name: string;
    slug: string;
  };
  export type SemesterListData = {
    semesters: Array<App.Data.Semester.SemesterData>;
  };
}
declare namespace App.Data.Session {
  export type SessionData = {
    id: number;
    name: string;
    slug: string;
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
    isAdmin: boolean;
  };
}
declare namespace App.Data.Students {
  export type StudentBasicData = {
    id: number;
    registrationNumber: string;
    lastName: string;
    firstName: string;
    otherNames: string;
    name: string;
    gender: App.Enums.Gender;
    birthDate: string;
    program: string;
    department: string;
    faculty: string;
    departmentProgram: string;
    admissionYear: number;
    nationality: string;
    slug: string;
    status: App.Enums.StudentStatus;
    statusColor: App.Enums.StatusColor;
    photoUrl: string;
  };
  export type StudentComprehensiveData = {
    student: App.Data.Students.StudentData;
    results: App.Data.Results.StudentResultData;
  };
  export type StudentData = {
    id: number;
    basic: App.Data.Students.StudentBasicData;
    others: App.Data.Students.StudentOtherData;
  };
  export type StudentOtherData = {
    id: number;
    state: string;
    localGovernment: string;
    entryMode: App.Enums.EntryMode;
    entrySession: string;
    entryLevel: string;
    jambRegistrationNumber: string;
    email: string;
    phoneNumber: string;
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
    student: App.Data.Students.StudentBasicData;
    fcgpa: string;
    resultsCount: number;
  };
}
declare namespace App.Data.Vetting {
  export type PaginatedVettingListData = {
    paginated: Array<App.Data.Vetting.VettingStudentData>;
  };
  export type VettingListData = {
    faculty: App.Data.Faculty.FacultyData;
    department: App.Data.Department.DepartmentData;
    graduands: Array<App.Data.Vetting.VettingStudentData>;
  };
  export type VettingReportData = {
    id: number;
    vettableId: number;
    vettableType: string;
    message: string;
  };
  export type VettingStepData = {
    id: number;
    type: App.Enums.VettingType;
    title: string;
    description: string;
    status: App.Enums.VettingStatus;
    color: App.Enums.StatusColor;
    reports: Array<App.Data.Vetting.VettingReportData>;
  };
  export type VettingStepListData = {
    items: Array<App.Data.Vetting.VettingStepData>;
  };
  export type VettingStudentData = {
    id: number;
    name: string;
    registrationNumber: string;
    studentStatus: App.Enums.StudentStatus;
    vettingStatus: App.Enums.VettingEventStatus;
    vettingStatusColor: App.Enums.StatusColor;
    slug: string;
  };
}
declare namespace App.Enums {
  export type ChecklistType =
    | "registration_number"
    | "in_course"
    | "exam"
    | "total"
    | "grade"
    | "credit_unit"
    | "semester"
    | "session"
    | "course_code"
    | "year"
    | "month"
    | "curriculum"
    | "entry_mode"
    | "entry_session"
    | "level"
    | "course_type";
  export type ClassOfDegree =
    | "FIRST CLASS HONOURS"
    | "SECOND CLASS HONOURS (UPPER DIVISION)"
    | "SECOND CLASS HONOURS (LOWER DIVISION)"
    | "THIRD CLASS HONOURS"
    | "PASS"
    | "FAIL";
  export type ComputationStrategy = "semester" | "universal";
  export type CourseStatus = "F" | "R";
  export type CourseType = "C" | "E" | "G" | "A" | "R";
  export type CreditUnit = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12 | 15 | 16 | 18 | 24 | 30;
  export type EntryMode = "UTME" | "DENT" | "PD" | "TRAN";
  export type ExcelImportType = "curriculum" | "final_result";
  export type Gender = "M" | "F" | "U";
  export type Grade = "A" | "B" | "C" | "D" | "E" | "F";
  export type ImportEventMethod =
    | "all"
    | "course"
    | "department"
    | "department_session"
    | "department_session_level"
    | "department_session_semester"
    | "registration_number"
    | "registration_number_session_semester"
    | "session"
    | "session_course";
  export type ImportEventStatus =
    | "new"
    | "queued"
    | "started"
    | "downloading"
    | "downloaded"
    | "uploading"
    | "uploaded"
    | "saving"
    | "saved"
    | "processing"
    | "cancelled"
    | "failed"
    | "completed";
  export type ImportEventType = "results" | "courses" | "departments" | "students" | "registrations";
  export type LevelEnum = "100" | "200" | "300" | "400" | "500" | "600";
  export type Months =
    | "January"
    | "February"
    | "March"
    | "April"
    | "May"
    | "June"
    | "July"
    | "August"
    | "September"
    | "October"
    | "November"
    | "December";
  export type NotificationType = "success" | "error" | "warning" | "info";
  export type ProgramDuration = 3 | 4 | 5 | 6;
  export type RawDataStatus = "pending" | "duplicate" | "failed" | "processed";
  export type RecordActionType = "create" | "update" | "delete";
  export type RecordSource = "portal" | "excel" | "legacy" | "system" | "user";
  export type ResultRemark = "PAS" | "FAL" | "ABS" | "MAL";
  export type Role = "super-admin" | "admin" | "desk-officer" | "exam-officer" | "database-officer" | "user";
  export type ScoreType = "course_work" | "exam";
  export type StatusColor = "gray" | "red" | "yellow" | "green" | "blue" | "purple" | "indigo" | "pink";
  export type StudentField = "phone_number" | "email" | "jamb_registration_number";
  export type StudentRelatedField = "program_id" | "entry_level_id" | "entry_session_id" | "local_government_id";
  export type StudentStatus =
    | "new"
    | "active"
    | "inactive"
    | "probation"
    | "withdrawn"
    | "expelled"
    | "suspended"
    | "deceased"
    | "unknown"
    | "transferred"
    | "final"
    | "extra"
    | "cleared"
    | "graduated";
  export type VettingEventStatus = "new" | "pending" | "vetting" | "failed" | "passed";
  export type VettingStatus = "new" | "checking" | "unchecked" | "failed" | "passed";
  export type VettingType =
    | "organize_year"
    | "validate_result"
    | "match_semesters"
    | "semester_credit"
    | "match_courses"
    | "credit_units"
    | "first_year"
    | "core_courses"
    | "elective_courses"
    | "failed_courses";
  export type Year = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12 | 13 | 14;
}
declare namespace App.Enums.ModifiableFields {
  export type CurriculumModifiableField =
    | "minimum_credit_units"
    | "maximum_credit_units"
    | "minimum_elective_count"
    | "minimum_elective_units"
    | "course"
    | "course_type"
    | "credit_unit";
  export type ProgramModifiableField = "code" | "name" | "faculty" | "duration" | "department" | "program_type";
  export type StudentModifiableField =
    | "name"
    | "email"
    | "gender"
    | "result"
    | "status"
    | "program"
    | "entry_mode"
    | "entry_level"
    | "phone_number"
    | "entry_session"
    | "date_of_birth"
    | "local_government"
    | "registration_number"
    | "jamb_registration_number";
}
declare namespace App.ViewModels.Clearance {
  export type ClearanceFormPage = {
    examOfficers: App.Data.ExamOfficer.ExamOfficerListData;
    years: App.Data.Clearance.ClearanceYearListData;
    months: App.Data.Clearance.ClearanceMonthListData;
  };
}
declare namespace App.ViewModels.Downloads {
  export type DownloadCoursesPage = {
    events: any;
    pending: App.Data.Imports.PendingImportEventData;
  };
  export type DownloadRegistrationPage = {
    departments: App.Data.Department.DepartmentListData;
    sessions: App.Data.Session.SessionListData;
    semesters: App.Data.Semester.SemesterListData;
    courses: App.Data.Course.CourseListData;
    levels: App.Data.Level.LevelListData;
    events: any;
    pending: App.Data.Imports.PendingImportEventData;
    selectedIndex: number;
  };
  export type DownloadStudentPage = {
    department: App.Data.Department.DepartmentListData;
    session: App.Data.Session.SessionListData;
    events: any;
    pending: App.Data.Imports.PendingImportEventData;
    selectedIndex: number;
  };
}
declare namespace App.ViewModels.Exports {
  export type ExportResultsPage = {
    departments: App.Data.Department.DepartmentListData;
    sessions: App.Data.Session.SessionListData;
    selectedIndex: number;
  };
}
declare namespace App.ViewModels.Imports {
  export type CurriculumImportPage = {
    data: App.Data.Imports.ExcelImportEventListData;
    departments: App.Data.Department.DepartmentListData;
  };
  export type ExcelImportPage = {
    data: App.Data.Imports.ExcelImportEventListData;
  };
}
declare namespace App.ViewModels.Reports {
  export type ClearedIndexPageData = {
    departments: App.Data.Department.DepartmentListData;
    students: App.Data.Cleared.ClearedStudentListData;
  };
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
    student: App.Data.Students.StudentBasicData;
    results: App.Data.Results.StudentResultData;
  };
}
declare namespace App.ViewModels.Students {
  export type StudentIndexPage = {
    paginated: Array<App.Data.Students.StudentBasicData>;
  };
  export type StudentShowPage = {
    data: App.Data.Students.StudentComprehensiveData;
    statues: App.Data.Enums.StudentStatusListData;
    selectedIndex: number;
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
declare namespace App.ViewModels.Vetting {
  export type VettingIndexPage = {
    departments: App.Data.Department.DepartmentListData;
    clearance: App.ViewModels.Clearance.ClearanceFormPage;
    steps: App.Data.Vetting.VettingStepListData;
    department: App.Data.Department.DepartmentInfoData;
    data: App.Data.Vetting.PaginatedVettingListData;
  };
}
declare namespace App.ViewModels.finalResults {
  export type FinalResultsIndexPage = {
    student: App.Data.Students.StudentBasicData;
    results: App.Data.FinalResults.FinalStudentResultData;
  };
}

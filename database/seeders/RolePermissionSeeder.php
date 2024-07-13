<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the list of roles
        $listOfRoles = [
            'Super Admin',
            'District Admin',
            'Municipality Admin',
            'Head School',
            'School Admin',
            'Teacher',
            'Accountant',
            'Librarian',
            'Principal',
            'Receptionist',
            'Student',
        ];
        // Define the list of permissions
        $arrayOfPermissionNames = [
            'create_marks_grades',
            'list_marks_grades',
            'edit_marks_grades',
            'delete_marks_grades',
            'create_marks_divisions',
            'list_marks_divisions',
            'edit_marks_divisions',
            'delete_marks_divisions',
            'create_departments',
            'list_departments',
            'edit_departments',
            'delete_departments',
            'create_designations',
            'list_designations',
            'edit_designations',
            'delete_designations',
            'create_district_users',
            'list_district_users',
            'edit_district_users',
            'delete_district_users',
            'create_inclusive_quotas',
            'list_inclusive_quotas',
            'edit_inclusive_quotas',
            'delete_inclusive_quotas',
            'create_academic_sessions',
            'list_academic_sessions',
            'edit_academic_sessions',
            'delete_academic_sessions',
            'create_permissions',
            'list_permissions',
            'edit_permissions',
            'delete_permissions',
            'create_roles',
            'list_roles',
            'edit_roles',
            'delete_roles',
            'create_expenses_head',
            'list_expenses_head',
            'edit_expenses_head',
            'delete_expenses_head',
            'create_expenses',
            'list_expenses',
            'edit_expenses',
            'delete_expenses',
            'create_income_head',
            'list_income_head',
            'edit_income_head',
            'delete_income_head',
            'create_incomes',
            'list_incomes',
            'edit_incomes',
            'delete_incomes',
            'create_inventory_head',
            'list_inventory_head',
            'edit_inventory_head',
            'delete_inventory_head',
            'create_inventories',
            'list_inventories',
            'edit_inventories',
            'delete_inventories',
            'create_attendance_types',
            'list_attendance_types',
            'edit_attendance_types',
            'delete_attendance_types',
            'create_leave_types',
            'list_leave_types',
            'edit_leave_types',
            'delete_leave_types',
            'create_fee_types',
            'list_fee_types',
            'edit_fee_types',
            'delete_fee_types',
            'create_fee_dues',
            'list_fee_dues',
            'edit_fee_dues',
            'delete_fee_dues',
            'create_fee_groups',
            'list_fee_groups',
            'edit_fee_groups',
            'delete_fee_groups',
            'create_fee_grouptypes',
            'list_fee_grouptypes',
            'edit_fee_grouptypes',
            'delete_fee_grouptypes',
            'create_fee_collections',
            'list_fee_collections',
            'edit_fee_collections',
            'delete_fee_collections',
            'create_classes',
            'list_classes',
            'edit_classes',
            'delete_classes',
            'create_sections',
            'list_sections',
            'edit_sections',
            'delete_sections',
            'create_schools',
            'list_schools',
            'edit_schools',
            'delete_schools',
            'create_school_groups',
            'list_school_groups',
            'edit_school_groups',
            'delete_school_groups',
            'create_subject_groups',
            'list_subject_groups',
            'edit_subject_groups',
            'delete_subject_groups',
            'create_subjects',
            'list_subjects',
            'edit_subjects',
            'delete_subjects',
            'create_class_timetables',
            'list_class_timetables',
            'edit_class_timetables',
            'delete_class_timetables',
            'create_assign_classteachers',
            'list_assign_classteachers',
            'edit_assign_classteachers',
            'delete_assign_classteachers',
            'create_teacher_timetables',
            'list_teacher_timetables',
            'edit_teacher_timetables',
            'delete_teacher_timetables',
            'create_students',
            'list_students',
            'edit_students',
            'delete_students',
            'create_student_sessions',
            'list_student_sessions',
            'edit_student_sessions',
            'delete_student_sessions',
            'create_staffs',
            'list_staffs',
            'edit_staffs',
            'delete_staffs',
            'create_staffs_leavedetails',
            'list_staffs_leavedetails',
            'edit_staffs_leavedetails',
            'delete_staffs_leavedetails',
            'create_staffs_resignation_details',
            'list_staffs_resignation_details',
            'edit_staffs_resignation_details',
            'delete_staffs_resignation_details',
            'create_municipality_users',
            'list_municipality_users',
            'edit_municipality_users',
            'delete_municipality_users',
            'create_head_schoolusers',
            'list_head_schoolusers',
            'edit_head_schoolusers',
            'delete_head_schoolusers',
            'create_school_adminusers',
            'list_school_adminusers',
            'edit_school_adminusers',
            'delete_school_adminusers',
            'create_lessons',
            'list_lessons',
            'edit_lessons',
            'delete_lessons',
            'create_topics',
            'list_topics',
            'edit_topics',
            'delete_topics',
            'create_lesson_plans',
            'list_lesson_plans',
            'edit_lesson_plans',
            'delete_lesson_plans',
            'create_student_attendances',
            'list_student_attendances',
            'edit_student_attendances',
            'delete_student_attendances',
            'create_student_leaverequests',
            'list_student_leaverequests',
            'edit_student_leaverequests',
            'delete_student_leaverequests',
            'create_staff_attendance',
            'list_staff_attendance',
            'edit_staff_attendance',
            'delete_staff_attendance',
            'create_staff_leaverequests',
            'list_staff_leaverequests',
            'edit_staff_leaverequests',
            'delete_staff_leaverequests',
            'create_examinations',
            'list_examinations',
            'edit_examinations',
            'delete_examinations',
            'create_exam_schedules',
            'list_exam_schedules',
            'edit_exam_schedules',
            'delete_exam_schedules',
            'create_exam_students',
            'list_exam_students',
            'edit_exam_students',
            'delete_exam_students',
            'create_exam_results',
            'list_exam_results',
            'edit_exam_results',
            'delete_exam_results',
            'create_generate_results',
            'list_generate_results',
            'edit_generate_results',
            'delete_generate_results',
            'create_certificates',
            'list_certificates',
            'edit_certificates',
            'delete_certificates',
            'create_idcards',
            'list_idcards',
            'edit_idcards',
            'delete_idcards',
            'create_staffidcards',
            'list_staffidcards',
            'edit_staffidcards',
            'delete_staffidcards',
            'create_admit_carddesigns',
            'list_admit_carddesigns',
            'edit_admit_carddesigns',
            'delete_admit_carddesigns',
            'create_mark_sheetdesigns',
            'list_mark_sheetdesigns',
            'edit_mark_sheetdesigns',
            'delete_mark_sheetdesigns',
            'create_teacher_logs',
            'list_teacher_logs',
            'edit_teacher_logs',
            'delete_teacher_logs',
            'create_headteacher_logs',
            'list_headteacher_logs',
            'edit_headteacher_logs',
            'delete_headteacher_logs',
            'create_headteacherlog_reports',
            'list_headteacherlog_reports',
            'edit_headteacherlog_reports',
            'delete_headteacherlog_reports',
            'list_student_certificates',
            'edit_student_certificates',
            'delete_student_certificates',
            'create_student_certificates',
            'list_school_houses',
            'edit_school_houses',
            'delete_school_houses',
            'create_school_houses',
            'list_exam_routines',
            'edit_exam_routines',
            'delete_exam_routines',
            'create_exam_routines',
            'list_assign_students',
            'edit_assign_students',
            'delete_assign_students',
            'create_assign_students',
            'create_assign_teachers',
            'list_assign_teachers',
            'edit_assign_teachers',
            'delete_assign_teachers',
            'create_primary_examinations',
            'list_primary_examinations',
            'edit_primary_examinations',
            'delete_primary_examinations',
            'create_primary_lessonmarks',
            'list_primary_lessonmarks',
            'edit_primary_lessonmarks',
            'delete_primary_lessonmarks',
            'list_primaryexam_routines',
            'edit_primaryexam_routines',
            'delete_primaryexam_routines',
            'create_primaryexam_routines',
            'create_assign_primarystudents',
            'list_assign_primarystudents',
            'edit_assign_primarystudents',
            'delete_assign_primarystudents',
            'create_generate_primaryresults',
            'list_generate_primaryresults',
            'edit_generate_primaryresults',
            'delete_generate_primaryresults',
            'create_generate_marksheets',
            'list_generate_marksheets',
            'edit_generate_marksheets',
            'delete_generate_marksheets',
            'create_generate_admitcards',
            'list_generate_admitcards',
            'edit_generate_admitcards',
            'delete_generate_admitcards',
            'create_students_import',
            'list_students_import',
            'edit_students_import',
            'delete_students_import',
            'create_staffs_import',
            'list_staffs_import',
            'edit_staffs_import',
            'delete_staffs_import',
            'create_students_additionalinformations',
            'edit_students_additionalinformations',
            'delete_students_additionalinformations',
            'list_students_additionalinformations',
            'create_extracurricular_head',
            'list_extracurricular_head',
            'edit_extracurricular_head',
            'delete_extracurricular_head',
            'list_attendence_report',
            'list_studentattendence_report',
            'list_inventory_report',


        ];

        // Create the permissions
        foreach ($arrayOfPermissionNames as $permissionName) {
            Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
        }
        // define permission for super admin
        $permissionIdsForSuperAdminRole = [
            'create_marks_grades',
            'list_marks_grades',
            'edit_marks_grades',
            'delete_marks_grades',
            'create_marks_divisions',
            'list_marks_divisions',
            'edit_marks_divisions',
            'delete_marks_divisions',
            'create_departments',
            'list_departments',
            'edit_departments',
            'delete_departments',
            'create_designations',
            'list_designations',
            'edit_designations',
            'delete_designations',
            'create_district_users',
            'list_district_users',
            'edit_district_users',
            'delete_district_users',
            'create_inclusive_quotas',
            'list_inclusive_quotas',
            'edit_inclusive_quotas',
            'delete_inclusive_quotas',
            'create_academic_sessions',
            'list_academic_sessions',
            'edit_academic_sessions',
            'delete_academic_sessions',
            'create_permissions',
            'list_permissions',
            'edit_permissions',
            'delete_permissions',
            'create_roles',
            'list_roles',
            'edit_roles',
            'delete_roles',
            'create_expenses_head',
            'list_expenses_head',
            'edit_expenses_head',
            'delete_expenses_head',
            'create_income_head',
            'list_income_head',
            'edit_income_head',
            'delete_income_head',
            'create_attendance_types',
            'list_attendance_types',
            'edit_attendance_types',
            'delete_attendance_types',
            'create_leave_types',
            'list_leave_types',
            'edit_leave_types',
            'delete_leave_types',
            'create_fee_types',
            'list_fee_types',
            'edit_fee_types',
            'delete_fee_types',
            'create_schools',
            'list_schools',
            'edit_schools',
            'delete_schools',
            'create_school_groups',
            'list_school_groups',
            'edit_school_groups',
            'delete_school_groups',

        ];

        //define permission for District Admin
        $permissionForDistrictAdmin = [
            'create_marks_grades',
            'list_marks_grades',
            'edit_marks_grades',
            'delete_marks_grades',
            'create_marks_divisions',
            'list_marks_divisions',
            'edit_marks_divisions',
            'delete_marks_divisions',
            'create_departments',
            'list_departments',
            'edit_departments',
            'delete_departments',
            'create_designations',
            'list_designations',
            'edit_designations',
            'delete_designations',
            'create_district_users',
            'list_district_users',
            'edit_district_users',
            'delete_district_users',
            'create_inclusive_quotas',
            'list_inclusive_quotas',
            'edit_inclusive_quotas',
            'delete_inclusive_quotas',
            'create_academic_sessions',
            'list_academic_sessions',
            'edit_academic_sessions',
            'delete_academic_sessions',
            'create_permissions',
            'list_permissions',
            'edit_permissions',
            'delete_permissions',
            'create_roles',
            'list_roles',
            'edit_roles',
            'delete_roles',
            'create_expenses_head',
            'list_expenses_head',
            'edit_expenses_head',
            'delete_expenses_head',
            'create_income_head',
            'list_income_head',
            'edit_income_head',
            'delete_income_head',
            'create_attendance_types',
            'list_attendance_types',
            'edit_attendance_types',
            'delete_attendance_types',
            'create_leave_types',
            'list_leave_types',
            'edit_leave_types',
            'delete_leave_types',
            'create_fee_types',
            'list_fee_types',
            'edit_fee_types',
            'delete_fee_types',
            'create_municipality_users',
            'list_municipality_users',
            'edit_municipality_users',
            'delete_municipality_users',
            'create_teacher_logs',
            'list_teacher_logs',
            'edit_teacher_logs',
            'delete_teacher_logs',
            'create_headteacher_logs',
            'list_headteacher_logs',
            'edit_headteacher_logs',
            'delete_headteacher_logs',
            'create_schools',
            'list_schools',
            'edit_schools',
            'delete_schools',
            'create_school_groups',
            'list_school_groups',
            'edit_school_groups',
            'delete_school_groups',


        ];

        //define permission for Municipility Admin
        $permissionForMunicipilityAdmin = [
            'create_marks_grades',
            'list_marks_grades',
            'edit_marks_grades',
            'delete_marks_grades',
            'create_marks_divisions',
            'list_marks_divisions',
            'edit_marks_divisions',
            'delete_marks_divisions',
            'create_departments',
            'list_departments',
            'edit_departments',
            'delete_departments',
            'create_designations',
            'list_designations',
            'edit_designations',
            'delete_designations',
            'create_inclusive_quotas',
            'list_inclusive_quotas',
            'edit_inclusive_quotas',
            'delete_inclusive_quotas',
            'create_academic_sessions',
            'list_academic_sessions',
            'edit_academic_sessions',
            'delete_academic_sessions',
            'create_permissions',
            'list_permissions',
            'edit_permissions',
            'delete_permissions',
            'create_roles',
            'list_roles',
            'edit_roles',
            'delete_roles',
            'create_expenses_head',
            'list_expenses_head',
            'edit_expenses_head',
            'delete_expenses_head',
            'create_income_head',
            'list_income_head',
            'edit_income_head',
            'delete_income_head',
            'create_inventory_head',
            'list_inventory_head',
            'edit_inventory_head',
            'delete_inventory_head',
            'list_inventories',
            'create_attendance_types',
            'list_attendance_types',
            'edit_attendance_types',
            'delete_attendance_types',
            'create_leave_types',
            'list_leave_types',
            'edit_leave_types',
            'delete_leave_types',
            'create_fee_types',
            'list_fee_types',
            'edit_fee_types',
            'delete_fee_types',
            'create_municipality_users',
            'list_municipality_users',
            'edit_municipality_users',
            'delete_municipality_users',
            'create_head_schoolusers',
            'list_head_schoolusers',
            'edit_head_schoolusers',
            'delete_head_schoolusers',
            'create_school_adminusers',
            'list_school_adminusers',
            'edit_school_adminusers',
            'delete_school_adminusers',
            // 'create_teacher_logs',
            'list_teacher_logs',
            // 'edit_teacher_logs',
            // 'delete_teacher_logs',
            // 'create_headteacher_logs',
            'list_headteacher_logs',
            // 'edit_headteacher_logs',
            // 'delete_headteacher_logs',
            'create_schools',
            'list_schools',
            'edit_schools',
            'delete_schools',
            'create_school_groups',
            'list_school_groups',
            'edit_school_groups',
            'delete_school_groups',
            'create_extracurricular_head',
            'list_extracurricular_head',
            'edit_extracurricular_head',
            'delete_extracurricular_head',
            'list_attendence_report',
            'list_inventory_report',

        ];

        //define permission for School Head Admin
        $permissionForSchoolHeadAdmin = [
            'create_marks_grades',
            'list_marks_grades',
            'edit_marks_grades',
            'delete_marks_grades',
            'create_marks_divisions',
            'list_marks_divisions',
            'edit_marks_divisions',
            'delete_marks_divisions',
            'create_expenses',
            'list_expenses',
            'edit_expenses',
            'delete_expenses',
            'create_incomes',
            'list_incomes',
            'edit_incomes',
            'delete_incomes',
            'list_income_head',
            'create_fee_groups',
            'list_fee_groups',
            'edit_fee_groups',
            'delete_fee_groups',
            'create_fee_dues',
            'list_fee_dues',
            'edit_fee_dues',
            'delete_fee_dues',
            'create_fee_collections',
            'list_fee_collections',
            'edit_fee_collections',
            'delete_fee_collections',
            'create_schools',
            'list_schools',
            'edit_schools',
            'delete_schools',
            'create_students',
            'list_students',
            'edit_students',
            'delete_students',
            'create_student_sessions',
            'list_student_sessions',
            'edit_student_sessions',
            'delete_student_sessions',
            'create_staffs',
            'list_staffs',
            'edit_staffs',
            'delete_staffs',
            'create_head_schoolusers',
            'list_head_schoolusers',
            'edit_head_schoolusers',
            'delete_head_schoolusers',
            'create_school_adminusers',
            'list_school_adminusers',
            'edit_school_adminusers',
            'delete_school_adminusers',
            'create_student_attendances',
            'list_student_attendances',
            'edit_student_attendances',
            'delete_student_attendances',
            'create_student_leaverequests',
            'list_student_leaverequests',
            'edit_student_leaverequests',
            'delete_student_leaverequests',
            'create_staff_attendance',
            'list_staff_attendance',
            'edit_staff_attendance',
            'delete_staff_attendance',
            'create_staff_leaverequests',
            'list_staff_leaverequests',
            'edit_staff_leaverequests',
            'delete_staff_leaverequests',
            'create_exam_results',
            'list_exam_results',
            'edit_exam_results',
            'delete_exam_results',
            'create_teacher_logs',
            'list_teacher_logs',
            'edit_teacher_logs',
            'delete_teacher_logs',
            'create_headteacher_logs',
            'list_headteacher_logs',
            'edit_headteacher_logs',
            'delete_headteacher_logs',
            'list_student_certificates',
            'edit_student_certificates',
            'delete_student_certificates',
            'create_student_certificates',
            'list_extracurricular_head',
        ];

        //define permission for School  Admin
        $permissionForSchoolAdmin = [
            'create_expenses',
            'list_expenses',
            'edit_expenses',
            'delete_expenses',
            'create_incomes',
            'list_incomes',
            'edit_incomes',
            'delete_incomes',
            'create_fee_groups',
            'list_fee_groups',
            'edit_fee_groups',
            'delete_fee_groups',
            'create_fee_grouptypes',
            'list_fee_grouptypes',
            'edit_fee_grouptypes',
            'delete_fee_grouptypes',
            'create_fee_dues',
            'list_fee_dues',
            'edit_fee_dues',
            'delete_fee_dues',
            'create_fee_collections',
            'list_fee_collections',
            'edit_fee_collections',
            'delete_fee_collections',
            'create_classes',
            'list_classes',
            'edit_classes',
            'delete_classes',
            'create_sections',
            'list_sections',
            'edit_sections',
            'delete_sections',
            'create_subject_groups',
            'list_subject_groups',
            'edit_subject_groups',
            'delete_subject_groups',
            'create_subjects',
            'list_subjects',
            'edit_subjects',
            'delete_subjects',
            'create_class_timetables',
            'list_class_timetables',
            'edit_class_timetables',
            'delete_class_timetables',
            'create_assign_classteachers',
            'list_assign_classteachers',
            'edit_assign_classteachers',
            'delete_assign_classteachers',
            'create_teacher_timetables',
            'list_teacher_timetables',
            'edit_teacher_timetables',
            'delete_teacher_timetables',
            'create_students',
            'list_students',
            'edit_students',
            'delete_students',
            'create_student_sessions',
            'list_student_sessions',
            'edit_student_sessions',
            'delete_student_sessions',
            'create_staffs',
            'list_staffs',
            'edit_staffs',
            'delete_staffs',
            'create_staffs_leavedetails',
            'list_staffs_leavedetails',
            'edit_staffs_leavedetails',
            'delete_staffs_leavedetails',
            'create_staffs_resignation_details',
            'list_staffs_resignation_details',
            'edit_staffs_resignation_details',
            'delete_staffs_resignation_details',
            'create_school_adminusers',
            'list_school_adminusers',
            'edit_school_adminusers',
            'delete_school_adminusers',
            'create_lessons',
            'list_lessons',
            'edit_lessons',
            'delete_lessons',
            'create_topics',
            'list_topics',
            'edit_topics',
            'delete_topics',
            'create_lesson_plans',
            'list_lesson_plans',
            'edit_lesson_plans',
            'delete_lesson_plans',
            'create_student_attendances',
            'list_student_attendances',
            'edit_student_attendances',
            'delete_student_attendances',
            'create_student_leaverequests',
            'list_student_leaverequests',
            'edit_student_leaverequests',
            'delete_student_leaverequests',
            'create_staff_attendance',
            'list_staff_attendance',
            'edit_staff_attendance',
            'delete_staff_attendance',
            'create_staff_leaverequests',
            'list_staff_leaverequests',
            'edit_staff_leaverequests',
            'delete_staff_leaverequests',
            'create_examinations',
            'list_examinations',
            'edit_examinations',
            'delete_examinations',
            'create_exam_schedules',
            'list_exam_schedules',
            'edit_exam_schedules',
            'delete_exam_schedules',
            'create_exam_students',
            'list_exam_students',
            'edit_exam_students',
            'delete_exam_students',
            'create_exam_results',
            'list_exam_results',
            'edit_exam_results',
            'delete_exam_results',
            'create_certificates',
            'list_certificates',
            'edit_certificates',
            'delete_certificates',
            'create_idcards',
            'list_idcards',
            'edit_idcards',
            'delete_idcards',
            'create_staffidcards',
            'list_staffidcards',
            'edit_staffidcards',
            'delete_staffidcards',
            'create_admit_carddesigns',
            'list_admit_carddesigns',
            'edit_admit_carddesigns',
            'delete_admit_carddesigns',
            'create_mark_sheetdesigns',
            'list_mark_sheetdesigns',
            'edit_mark_sheetdesigns',
            'delete_mark_sheetdesigns',
            'create_teacher_logs',
            'list_teacher_logs',
            'edit_teacher_logs',
            'delete_teacher_logs',
            'create_headteacher_logs',
            'list_headteacher_logs',
            'edit_headteacher_logs',
            'delete_headteacher_logs',
            'create_headteacherlog_reports',
            'list_headteacherlog_reports',
            'edit_headteacherlog_reports',
            'delete_headteacherlog_reports',
            'list_student_certificates',
            'edit_student_certificates',
            'delete_student_certificates',
            'create_student_certificates',
            'list_school_houses',
            'edit_school_houses',
            'delete_school_houses',
            'create_school_houses',
            'list_exam_routines',
            'edit_exam_routines',
            'delete_exam_routines',
            'create_exam_routines',
            'create_assign_students',
            'list_assign_students',
            'edit_assign_students',
            'delete_assign_students',
            'create_assign_teachers',
            'list_assign_teachers',
            'edit_assign_teachers',
            'delete_assign_teachers',
            'create_generate_results',
            'list_generate_results',
            'edit_generate_results',
            'delete_generate_results',
            'create_primary_examinations',
            'list_primary_examinations',
            'edit_primary_examinations',
            'delete_primary_examinations',
            'create_primary_lessonmarks',
            'list_primary_lessonmarks',
            'edit_primary_lessonmarks',
            'delete_primary_lessonmarks',
            'list_primaryexam_routines',
            'edit_primaryexam_routines',
            'delete_primaryexam_routines',
            'create_primaryexam_routines',
            'create_assign_primarystudents',
            'list_assign_primarystudents',
            'edit_assign_primarystudents',
            'delete_assign_primarystudents',
            'create_generate_primaryresults',
            'list_generate_primaryresults',
            'edit_generate_primaryresults',
            'delete_generate_primaryresults',
            'create_generate_marksheets',
            'list_generate_marksheets',
            'edit_generate_marksheets',
            'delete_generate_marksheets',
            'create_generate_admitcards',
            'list_generate_admitcards',
            'edit_generate_admitcards',
            'delete_generate_admitcards',
            'create_students_import',
            'list_students_import',
            'edit_students_import',
            'delete_students_import',
            'create_staffs_import',
            'list_staffs_import',
            'edit_staffs_import',
            'delete_staffs_import',
            'create_students_additionalinformations',
            'edit_students_additionalinformations',
            'delete_students_additionalinformations',
            'list_students_additionalinformations',
            'list_inventory_head',
            'create_inventories',
            'list_inventories',
            'edit_inventories',
            'delete_inventories',
            'list_extracurricular_head',
            'list_studentattendence_report',

        ];

        // Create roles and assign permissions to each role
        foreach ($listOfRoles as $roleName) {
            $role = Role::create(['name' => $roleName]);

            // Assign specific permissions based on role
            switch ($roleName) {
                case 'Super Admin':
                    $role->syncPermissions($permissionIdsForSuperAdminRole);
                    break;
                case 'District Admin':
                    $role->givePermissionTo($permissionForDistrictAdmin);
                    break;
                case 'Municipality Admin':
                    $role->givePermissionTo($permissionForMunicipilityAdmin);
                    break;
                case 'Head School':
                    $role->givePermissionTo($permissionForSchoolHeadAdmin);
                    break;
                case 'School Admin':
                    $role->givePermissionTo($permissionForSchoolAdmin);
                    break;
                // Add more cases for other roles as needed
            }
        }
    }
}
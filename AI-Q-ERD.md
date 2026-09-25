# AI-Q Entity Relationship Diagram

Verified against the live `scoreforge_ai` database on 2026-09-20. This diagram covers 15 application tables and their 21 foreign-key relationships. Framework infrastructure tables (sessions, cache, queues, and migration tracking) are outside this view. Selected business fields are shown; routine timestamps and authentication fields are omitted.

```mermaid
erDiagram
    direction LR
    users["users"] {
        bigint id PK
        varchar fname
        varchar lname
        varchar email UK
        enum role
    }
    classes["classes"] {
        bigint id PK
        varchar name
    }
    subjects["subjects"] {
        bigint id PK
        varchar name
        text description
    }
    exams["exams"] {
        bigint id PK
        bigint subject_id FK
        bigint class_id FK
        bigint created_by FK
        varchar title
        varchar exam_code UK
        int duration
        int passing_score
        int total_items
        enum status
    }
    questions["questions"] {
        bigint id PK
        bigint exam_id FK
        enum question_type
        text question_text
        varchar topic
        text correct_answer
        int points
    }
    questionOptions["question_options"] {
        bigint id PK
        bigint question_id FK
        text option_text
        boolean is_correct
    }
    examAttempts["exam_attempts"] {
        bigint id PK
        bigint exam_id FK
        bigint student_id FK
        decimal score
        decimal total_score
        timestamp started_at
        timestamp submitted_at
        enum status
    }
    answers["answers"] {
        bigint id PK
        bigint attempt_id FK
        bigint question_id FK
        text answer_text
        boolean is_correct
    }
    examAnswers["exam_answers"] {
        bigint id PK
        bigint attempt_id FK
        bigint question_id FK
        text student_answer
        boolean is_correct
    }
    studentAnalytics["student_analytics"] {
        bigint id PK
        bigint student_id FK
        decimal average_score
        decimal improvement_rate
        int rank_position
        timestamp last_updated
    }
    predictions["predictions"] {
        bigint id PK
        bigint student_id FK
        bigint subject_id FK "Nullable"
        decimal predicted_score
        varchar predicted_level
        varchar risk_level
        varchar confidence_level
        text reason
    }
    recommendations["recommendations"] {
        bigint id PK
        bigint student_id FK
        bigint subject_id FK
        text recommendation_text
    }
    academicReadiness["academic_readiness"] {
        bigint id PK
        bigint student_id FK, UK
        decimal readiness_score
        varchar readiness_level
        text reason
        timestamp evaluated_at
    }
    subjectPrerequisites["subject_prerequisites"] {
        bigint id PK
        bigint subject_id FK "Unique together with prerequisite_subject_id"
        bigint prerequisite_subject_id FK
    }
    aiInsights["ai_insights"] {
        bigint id PK
        bigint exam_id FK
        bigint generated_by FK
        varchar type
        text performance_summary
        longtext key_findings "JSON content"
        longtext recommended_actions "JSON content"
        varchar model
        timestamp generated_at
    }
    users ||..o{ exams : "creates"
    classes ||..o{ exams : "groups"
    subjects ||..o{ exams : "covers"
    exams ||..o{ questions : "contains"
    questions ||..o{ questionOptions : "offers"
    users ||..o{ examAttempts : "takes"
    exams ||..o{ examAttempts : "receives"
    examAttempts ||..o{ answers : "records"
    questions ||..o{ answers : "answered by"
    examAttempts ||..o{ examAnswers : "records separately"
    questions ||..o{ examAnswers : "answered by"
    users ||..o{ studentAnalytics : "has analytics"
    users ||..o{ predictions : "has predictions"
    subjects |o..o{ predictions : "optionally scopes"
    users ||..o{ recommendations : "receives"
    subjects ||..o{ recommendations : "scopes"
    users ||..o| academicReadiness : "has readiness"
    subjects ||..o{ subjectPrerequisites : "requires"
    subjects ||..o{ subjectPrerequisites : "is prerequisite"
    exams ||..o{ aiInsights : "has insights"
    users ||..o{ aiInsights : "generates"
```

## Reading the diagram

- **PK**: primary key; **FK**: foreign key; **UK**: unique key.
- `||`: exactly one; `o|` or `|o`: zero or one; `o{`: zero or many.
- Dotted relationships are non-identifying: each child has its own primary key rather than using its parent foreign key as part of that primary key.
- Teachers, students, and administrators share `users`; `role` distinguishes them. Role restrictions are enforced by application logic, not these foreign keys.

## Schema notes

- Both `answers` and `exam_answers` exist and reference attempts and questions. They are shown separately, not merged.
- `student_analytics.student_id` is not unique in the database, so the physical schema permits multiple rows per student even though the User model declares a one-to-one relationship.
- `academic_readiness.student_id` is unique, permitting at most one readiness record per student.
- `predictions.subject_id` is nullable, so a prediction can have no subject.
- `subject_prerequisites` has a unique constraint on the pair `(subject_id, prerequisite_subject_id)`, not on either column alone.
- There is no direct student-to-class enrollment foreign key in this schema. Classes connect to students through exams and attempts.
- The live database was used as the source of truth because the migrations contain two different `predictions` creation definitions and an empty user-name alteration migration.


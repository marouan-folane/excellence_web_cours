@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="m-0">
                            @if(session('locale') == 'fr')
                                Détails du Cours
                            @elseif(session('locale') == 'ar')
                                تفاصيل الدورة
                            @else
                                Course Details
                            @endif
                        </h3>
                        <a href="{{ route('courses.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            @if(session('locale') == 'fr')
                                Retour
                            @elseif(session('locale') == 'ar')
                                رجوع
                            @else
                                Back
                            @endif
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="mb-3">
                                @if(session('locale') == 'fr')
                                    Informations du Cours
                                @elseif(session('locale') == 'ar')
                                    معلومات الدورة
                                @else
                                    Course Information
                                @endif
                            </h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%" class="bg-light">
                                        @if(session('locale') == 'fr')
                                            Matière
                                        @elseif(session('locale') == 'ar')
                                            المادة
                                        @else
                                            Subject
                                        @endif
                                    </th>
                                    <td>{{ $course->matiere }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        @if(session('locale') == 'fr')
                                            Niveau Scolaire
                                        @elseif(session('locale') == 'ar')
                                            المستوى الدراسي
                                        @else
                                            School Level
                                        @endif
                                    </th>
                                    <td>
                                        @if(session('locale') == 'fr')
                                            @if($course->niveau_scolaire == 'premiere_school') Première School
                                            @elseif($course->niveau_scolaire == '1ac') 1ère Année Collège
                                            @elseif($course->niveau_scolaire == '2ac') 2ème Année Collège
                                            @elseif($course->niveau_scolaire == '3ac') 3ème Année Collège
                                            @elseif($course->niveau_scolaire == 'tronc_commun') Tronc Commun
                                            @elseif($course->niveau_scolaire == 'deuxieme_annee') 2ème Année Lycée
                                            @elseif($course->niveau_scolaire == 'bac') Baccalauréat
                                            @else {{ $course->niveau_scolaire }}
                                            @endif
                                        @elseif(session('locale') == 'ar')
                                            @if($course->niveau_scolaire == 'premiere_school') المدرسة الابتدائية
                                            @elseif($course->niveau_scolaire == '1ac') السنة الأولى إعدادي
                                            @elseif($course->niveau_scolaire == '2ac') السنة الثانية إعدادي
                                            @elseif($course->niveau_scolaire == '3ac') السنة الثالثة إعدادي
                                            @elseif($course->niveau_scolaire == 'tronc_commun') الجذع المشترك
                                            @elseif($course->niveau_scolaire == 'deuxieme_annee') السنة الثانية باكالوريا
                                            @elseif($course->niveau_scolaire == 'bac') باكالوريا
                                            @else {{ $course->niveau_scolaire }}
                                            @endif
                                        @else
                                            @if($course->niveau_scolaire == 'premiere_school') Primary School
                                            @elseif($course->niveau_scolaire == '1ac') 1st Middle School
                                            @elseif($course->niveau_scolaire == '2ac') 2nd Middle School
                                            @elseif($course->niveau_scolaire == '3ac') 3rd Middle School
                                            @elseif($course->niveau_scolaire == 'tronc_commun') Common Core
                                            @elseif($course->niveau_scolaire == 'deuxieme_annee') 2nd Year High School
                                            @elseif($course->niveau_scolaire == 'bac') Baccalaureate
                                            @else {{ $course->niveau_scolaire }}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        @if(session('locale') == 'fr')
                                            Prix
                                        @elseif(session('locale') == 'ar')
                                            السعر
                                        @else
                                            Price
                                        @endif
                                    </th>
                                    <td>{{ number_format($course->prix, 2) }} DH</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        @if(session('locale') == 'fr')
                                            Type
                                        @elseif(session('locale') == 'ar')
                                            النوع
                                        @else
                                            Type
                                        @endif
                                    </th>
                                    <td>
                                        @if(session('locale') == 'fr')
                                            {{ $course->type == 'regular' ? 'Régulier' : 'Communication' }}
                                        @elseif(session('locale') == 'ar')
                                            {{ $course->type == 'regular' ? 'منتظم' : 'تواصل' }}
                                        @else
                                            {{ $course->type == 'regular' ? 'Regular' : 'Communication' }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        @if(session('locale') == 'fr')
                                            Statistiques
                                        @elseif(session('locale') == 'ar')
                                            إحصائيات
                                        @else
                                            Statistics
                                        @endif
                                    </h5>
                                    <div class="mb-2">
                                        @if(session('locale') == 'fr')
                                            <strong>Nombre d'étudiants inscrits:</strong> {{ count($enrolledStudents) }}
                                        @elseif(session('locale') == 'ar')
                                            <strong>عدد الطلاب المسجلين:</strong> {{ count($enrolledStudents) }}
                                        @else
                                            <strong>Number of enrolled students:</strong> {{ count($enrolledStudents) }}
                                        @endif
                                    </div>
                                    <div>
                                        @if(session('locale') == 'fr')
                                            <strong>Revenu mensuel estimé:</strong> {{ number_format(count($enrolledStudents) * $course->prix, 2) }} DH
                                        @elseif(session('locale') == 'ar')
                                            <strong>الدخل الشهري المقدر:</strong> {{ number_format(count($enrolledStudents) * $course->prix, 2) }} DH
                                        @else
                                            <strong>Estimated monthly revenue:</strong> {{ number_format(count($enrolledStudents) * $course->prix, 2) }} DH
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="mt-4 mb-3">
                        @if(session('locale') == 'fr')
                            Étudiants Inscrits
                        @elseif(session('locale') == 'ar')
                            الطلاب المسجلون
                        @else
                            Enrolled Students
                        @endif
                    </h4>

                    @if(count($enrolledStudents) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>
                                            @if(session('locale') == 'fr')
                                                Nom
                                            @elseif(session('locale') == 'ar')
                                                الاسم
                                            @else
                                                Name
                                            @endif
                                        </th>
                                        <th>
                                            @if(session('locale') == 'fr')
                                                Email
                                            @elseif(session('locale') == 'ar')
                                                البريد الإلكتروني
                                            @else
                                                Email
                                            @endif
                                        </th>
                                        <th>
                                            @if(session('locale') == 'fr')
                                                Téléphone
                                            @elseif(session('locale') == 'ar')
                                                الهاتف
                                            @else
                                                Phone
                                            @endif
                                        </th>
                                        <th>
                                            @if(session('locale') == 'fr')
                                                Date d'inscription
                                            @elseif(session('locale') == 'ar')
                                                تاريخ التسجيل
                                            @else
                                                Enrollment Date
                                            @endif
                                        </th>
                                        <th>
                                            @if(session('locale') == 'fr')
                                                Date d'expiration
                                            @elseif(session('locale') == 'ar')
                                                تاريخ انتهاء الصلاحية
                                            @else
                                                Expiry Date
                                            @endif
                                        </th>
                                        <th>
                                            @if(session('locale') == 'fr')
                                                Statut
                                            @elseif(session('locale') == 'ar')
                                                الحالة
                                            @else
                                                Status
                                            @endif
                                        </th>
                                        <th>
                                            @if(session('locale') == 'fr')
                                                Actions
                                            @elseif(session('locale') == 'ar')
                                                إجراءات
                                            @else
                                                Actions
                                            @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledStudents as $student)
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->email ?? '-' }}</td>
                                            <td>{{ $student->phone ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($student->enrollment_date)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($student->payment_expiry)
                                                    {{ \Carbon\Carbon::parse($student->payment_expiry)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $student->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    @if(session('locale') == 'fr')
                                                        {{ $student->status == 'active' ? 'Actif' : 'Inactif' }}
                                                    @elseif(session('locale') == 'ar')
                                                        {{ $student->status == 'active' ? 'نشط' : 'غير نشط' }}
                                                    @else
                                                        {{ $student->status == 'active' ? 'Active' : 'Inactive' }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                    @if(session('locale') == 'fr')
                                                        Modifier
                                                    @elseif(session('locale') == 'ar')
                                                        تعديل
                                                    @else
                                                        Edit
                                                    @endif
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            @if(session('locale') == 'fr')
                                Aucun étudiant n'est inscrit à ce cours.
                            @elseif(session('locale') == 'ar')
                                لا يوجد طلاب مسجلين في هذه الدورة.
                            @else
                                No students are enrolled in this course.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional styling to ensure consistent appearance */
.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

.bg-light {
    background-color: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge.bg-success {
    color: #fff;
    background-color: #28a745;
}

.badge.bg-danger {
    color: #fff;
    background-color: #dc3545;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.25rem;
    margin-bottom: 1rem;
}

.card-header {
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.card-body {
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1.25rem;
}
</style>
@endsection 
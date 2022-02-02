import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AboutUsComponent } from './components/about-us/about-us.component';
import { AchievementsandeventsComponent } from './components/achievementsandevents/achievementsandevents.component';
import { ChangePasswordComponent } from './components/change-password/change-password.component';
import { ChaptersComponent } from './components/chapters/chapters.component';
import { ContactUsComponent } from './components/contact-us/contact-us.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { ExerciseComponent } from './components/exercise/exercise.component';
import { FaqComponent } from './components/faq/faq.component';
import { ForgotPasswordComponent } from './components/forgot-password/forgot-password.component';
import { HomeComponent } from './components/home/home.component';
import { LandingComponent } from './components/landing/landing.component';
import { AcknowledgementsComponent } from './components/modules/acknowledgements/acknowledgements.component';
import { AssessmentEvaluationComponent } from './components/modules/assessment-evaluation/assessment-evaluation.component';
import { CaseStudyComponent } from './components/modules/case-study/case-study.component';
import { IntroductionComponent } from './components/modules/introduction/introduction.component';
import { AfterFightingFireComponent } from './components/modules/module_1/after-fighting-fire/after-fighting-fire.component';
import { FirefighterCancerRiskComponent } from './components/modules/module_1/firefighter-cancer-risk/firefighter-cancer-risk.component';
import { LearningObjectivesComponent } from './components/modules/module_1/learning-objectives/learning-objectives.component';
import { MetaAnalysisComponent } from './components/modules/module_1/meta-analysis/meta-analysis.component';
import { NioshComponent } from './components/modules/module_1/niosh/niosh.component';
import { ScopeOfProblemComponent } from './components/modules/module_1/scope-of-problem/scope-of-problem.component';
import { FirefighterExposedComponent } from './components/modules/module_2/firefighter-exposed/firefighter-exposed.component';
import { OccupationalExposureComponent } from './components/modules/module_2/occupational-exposure/occupational-exposure.component';
import { OnSceneComponent } from './components/modules/module_2/on-scene/on-scene.component';
import { ToxinsFromComponent } from './components/modules/module_2/toxins-from/toxins-from.component';
import { QuestionComponent } from './components/modules/question/question.component';
import { ReducingRiskComponent } from './components/modules/reducing-risk/reducing-risk.component';
import { ReferencesComponent } from './components/modules/references/references.component';
import { ThankYouComponent } from './components/modules/thank-you/thank-you.component';
import { VideoComponent } from './components/modules/video/video.component';
import { PrivacyPolicyComponent } from './components/privacy-policy/privacy-policy.component';
import { ProfileComponent } from './components/profile/profile.component';
import { QuestionnaireComponent } from './components/questionnaire/questionnaire.component';
import { ResourcesComponent } from './components/resources/resources.component';
import { TermsConditionsComponent } from './components/terms-conditions/terms-conditions.component';
import { DemographicComponent } from './components/demographic/demographic.component';

import { PatientGuard } from './guards/patient.guard';
import { SurvivorGuard } from './guards/survivor.guard';
import { FireServiceComponent } from './components/modules/fire-service/fire-service.component';


const routes: Routes = [
	{ path: '', redirectTo: 'home', pathMatch: 'full' },
	{ path: 'home', component: HomeComponent },
	{ path: 'patient/dashboard', component: DashboardComponent, canActivate: [SurvivorGuard] },

	{ path: 'patient/profile', component: ProfileComponent, canActivate: [PatientGuard] },

	// Common Routes
	{ path: 'contact-us', component: ContactUsComponent },
	{ path: 'about-us', component: AboutUsComponent },
	{ path: 'faq', component: FaqComponent },
	{ path: 'terms-conditions', component: TermsConditionsComponent },
	{ path: 'privacy-policy', component: PrivacyPolicyComponent },
	{ path: 'resources', component: ResourcesComponent, canActivate: [SurvivorGuard] },
	{ path: 'create-password/:code', component: ChangePasswordComponent },
	{ path: 'reset-password/:code', component: ChangePasswordComponent },
	{ path: 'study-questionnaires/:code', component: QuestionnaireComponent, canActivate: [SurvivorGuard] },
	{ path: 'forgot-password', component: ForgotPasswordComponent },
	{ path: 'achievements', component: AchievementsandeventsComponent, canActivate: [SurvivorGuard] },

	// Dynamic Route Starts
	{ path: "patient/dashboard/:chapter", component: ChaptersComponent, canActivate: [PatientGuard] },
	{ path: "patient/dashboard/:chapter/:sub-topic", component: ChaptersComponent, canActivate: [PatientGuard] },
	// Dynamic Route Starts for exercises
	{ path: "patient/dashboard/:chapter/exercise/:exercise", component: ExerciseComponent, canActivate: [PatientGuard] },
	{ path: "patient/landing", component: LandingComponent, canActivate: [SurvivorGuard] },
	{ path: 'patient/:module/video/:index', component: VideoComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/scope-of-problem/:index', component: ScopeOfProblemComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/niosh/:index', component: NioshComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/meta-analysis', component: MetaAnalysisComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/intro', component: IntroductionComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/assessment-evaluation', component: AssessmentEvaluationComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/question/:index', component: QuestionComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/after-fighting-fire/:index', component: AfterFightingFireComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/firefighter-cancer-risk/:index', component: FirefighterCancerRiskComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/learning-objectives/:index', component: LearningObjectivesComponent, canActivate: [PatientGuard] }, 
	{ path: 'patient/:module/firefighter-exposed/:index', component: FirefighterExposedComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/occupational-exposure/:index', component: OccupationalExposureComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/toxins-from/:index', component: ToxinsFromComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/on-scene/:index', component: OnSceneComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/reduce-risk/:index', component: ReducingRiskComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/case-study/:index', component: CaseStudyComponent, canActivate: [PatientGuard] },
	{ path: 'patient/module-5/thank-you', component: ThankYouComponent, canActivate: [PatientGuard] },
	{ path: 'patient/modules/acknowledgements', component: AcknowledgementsComponent, canActivate: [PatientGuard] },
	{ path: 'patient/modules/references', component: ReferencesComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/fire-service/:index', component: FireServiceComponent, canActivate: [PatientGuard] },
	{ path: 'patient/:module/reduce-risk/:index', component: ReducingRiskComponent, canActivate: [PatientGuard] },
	//{ path: 'patient/modules/fire-service', component: FireServiceComponent, canActivate: [PatientGuard] }, 	
	{ path: 'patient/demographic', component: DemographicComponent, canActivate: [PatientGuard] }, 

]; 

@NgModule({
	imports: [RouterModule.forRoot(routes, { useHash: true })],
	exports: [RouterModule],
	providers: [],
})
export class AppRoutingModule { }

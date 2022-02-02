import { AgmCoreModule } from '@agm/core';
// import { AmChartsModule } from '@amcharts/amcharts3-angular';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgIdleKeepaliveModule } from '@ng-idle/keepalive'; // this includes the core NgIdleModule but includes keepalive providers for easy wireup
import { Ng4LoadingSpinnerModule } from 'ng4-loading-spinner';
// import { AccordionModule, BsDropdownModule, CarouselModule, CollapseModule } from 'ngx-bootstrap';
import { AccordionModule } from 'ngx-bootstrap/accordion';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { CarouselModule } from 'ngx-bootstrap/carousel';
import { CollapseModule } from 'ngx-bootstrap/collapse';
import { ModalModule } from 'ngx-bootstrap/modal';
import { EmbedVideo } from 'ngx-embed-video';
import { ToastrModule } from 'ngx-toastr';
import { NgxYoutubePlayerModule } from 'ngx-youtube-player';
import { VgBufferingModule } from 'videogular2/buffering';
import { VgControlsModule } from 'videogular2/controls';
import { VgCoreModule } from 'videogular2/core';
import { VgOverlayPlayModule } from 'videogular2/overlay-play';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
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
import { PreviousNextButtonsComponent } from './components/modules/previous-next-buttons/previous-next-buttons.component';
import { QuestionComponent } from './components/modules/question/question.component';
import { ReducingRiskComponent } from './components/modules/reducing-risk/reducing-risk.component';
import { ThankYouComponent } from './components/modules/thank-you/thank-you.component';
import { VideoComponent } from './components/modules/video/video.component';
import { PrivacyPolicyComponent } from './components/privacy-policy/privacy-policy.component';
import { ProfileComponent } from './components/profile/profile.component';
import { QuestionnaireComponent } from './components/questionnaire/questionnaire.component';
import { ResourcesComponent } from './components/resources/resources.component';
import { BreadcrumbComponent } from './components/sub-components/breadcrumb/breadcrumb.component';
import { FooterComponent } from './components/sub-components/footer/footer.component';
import { HeaderComponent } from './components/sub-components/header/header.component';
import { InnerPageHeaderComponent } from './components/sub-components/inner-page-header/inner-page-header.component';
import { InquiryMoodModalComponent } from './components/sub-components/inquiry-mood-modal/inquiry-mood-modal.component';
import { TermsConditionsComponent } from './components/terms-conditions/terms-conditions.component';
//add service
import { PatientGuard } from './guards/patient.guard';
import { SurvivorGuard } from './guards/survivor.guard';
// Add middleware
import { ResearcherGuard } from './guards/researcher.guard';
import { TokenInterceptor } from "./interceptor/TokenInterceptor";
import { SafeHtmlPipe } from './pipes/safe-html.pipe';
import { AuthService } from './service/auth.service';
import { DataService } from './service/data.service';
import { HelperService } from './service/helper.service';
import { QuestionnaireService } from './service/questionnaire.service';
import { AcknowledgementsComponent } from './components/modules/acknowledgements/acknowledgements.component';
import { ReferencesComponent } from './components/modules/references/references.component';
import { DemographicComponent } from './components/demographic/demographic.component';
import { FireServiceComponent } from './components/modules/fire-service/fire-service.component';

@NgModule({
	declarations: [
		AppComponent,
		HomeComponent,
		DashboardComponent,
		HeaderComponent,
		FooterComponent,
		ChangePasswordComponent,
		InnerPageHeaderComponent,
		ForgotPasswordComponent,
		ContactUsComponent,
		AboutUsComponent,
		FaqComponent,
		TermsConditionsComponent,
		ResourcesComponent,
		ProfileComponent,
		PrivacyPolicyComponent,
		ChaptersComponent,
		BreadcrumbComponent,
		ExerciseComponent,
		SafeHtmlPipe,
		AchievementsandeventsComponent,
		QuestionnaireComponent,
		InquiryMoodModalComponent,
		IntroductionComponent,
		QuestionComponent,
		VideoComponent,
		MetaAnalysisComponent,
		NioshComponent,
		AfterFightingFireComponent,
		FirefighterCancerRiskComponent,
		LearningObjectivesComponent,
		AssessmentEvaluationComponent,
		FirefighterExposedComponent,
		OccupationalExposureComponent,
		ToxinsFromComponent,
		OnSceneComponent,
		LandingComponent,
		PreviousNextButtonsComponent,
		ScopeOfProblemComponent,
		ReducingRiskComponent,
		CaseStudyComponent,
		ThankYouComponent,
		AcknowledgementsComponent,
		ReferencesComponent,
		FireServiceComponent,
		DemographicComponent
	],
	imports: [
		BrowserAnimationsModule,
		BrowserModule,
		AppRoutingModule,
		HttpClientModule,
		FormsModule,
		ModalModule.forRoot(),
		NgxYoutubePlayerModule.forRoot(),
		ToastrModule.forRoot({
			preventDuplicates: true,
			enableHtml: true,
		}),
		CarouselModule.forRoot(),
		ReactiveFormsModule,
		CollapseModule.forRoot(),
		AccordionModule.forRoot(),
		BsDropdownModule.forRoot(),
		AgmCoreModule.forRoot({
			apiKey: 'AIzaSyBuMbGtlIjsbsR1TGYvNY18iDFGWcB5eyk'
		}),
		Ng4LoadingSpinnerModule.forRoot(),
		NgIdleKeepaliveModule.forRoot(),
		VgControlsModule,
		VgCoreModule,
		VgOverlayPlayModule,
		VgBufferingModule,
		EmbedVideo.forRoot(),
	],
	providers: [HttpClientModule,
		QuestionnaireService,
		ResearcherGuard,
		PatientGuard,
		SurvivorGuard,
		AuthService,
		DataService,
		HelperService,
		{
			provide: HTTP_INTERCEPTORS,
			useClass: TokenInterceptor,
			multi: true,
		},
	],
	bootstrap: [AppComponent]
})
export class AppModule { }

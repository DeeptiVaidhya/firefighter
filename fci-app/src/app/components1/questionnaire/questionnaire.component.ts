import { Component, OnInit, TemplateRef } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BsModalRef, BsModalService } from "ngx-bootstrap/modal";
import { ToastrService } from "ngx-toastr";
import { QuestionnaireService } from "../../service/questionnaire.service";
import { AuthService } from "../../service/auth.service";
@Component({
    selector: "app-questionnaire",
    templateUrl: "./questionnaire.component.html",
    styleUrls: ["./questionnaire.component.css"]
})
export class QuestionnaireComponent implements OnInit {
    modalRef: BsModalRef;
    data: any;
    options = [];
    questions: any;
    answer: any;
    group_title: any;
    short_description: any;
    total_question_groups = 0;
    page_number = 1;
    type: string = "";
    groupId: any;
    symptom_percentage = 0;
    totalQuestions: number = 0;
    weekInfoId: any;
    confirm_submition: boolean = false;
    question_count = 0;
    breadcrumb = [
        { link: "/", title: "Home" },
        { title: "Questionnaire", class: "active" }
    ];

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        public toastr: ToastrService,
        public questionnaireService: QuestionnaireService,
        private authService: AuthService,
        private modalService: BsModalService
    ) { }
    /**
     * @desc Set all Pro-Ctcae Questions on Page View Init
     */
    ngOnInit() {

    }

    /**
     * @desc Get Pro-Ctcae Questionnaire for each patient depending on cancer type.
     */
    getQuestionnaire() {
        // this.questionnaireService
        //     .get_questionnaire({ type: this.type.toUpperCase() })
        //     .subscribe(Response => {
        //         if (Response["status"] == "success") {
        //             this.questions = Response["questions"];
        //             this.totalQuestions = this.questions.length;
        //             console.log(this.totalQuestions);
        //             this.options = Response["options"];
        //             this.groupId = Response["group_id"];
        //             this.group_title = Response["group_title"];
        //             this.weekInfoId = Response["week_info"]["id"];
        //             this.short_description = Response["short_description"];
        //         } else {
        //             this.toastr.error(Response["msg"]);
        //             this.router.navigate(["/patient/dashboard"]);
        //         }
        //     }); 
    }

    openModal(template: TemplateRef<any>) {
        this.modalRef = this.modalService.show(template);
    }

    confirmSubmission() {
        this.confirm_submition = true;
        this.save();
    }

    highlightQuestions() {
        this.modalRef.hide();
        let tr = document.querySelectorAll("tr.quest-row"), td;
        for (let i = 0, l = tr.length; i < l; i++) {
            td = tr[i].querySelector(".c-purple") as HTMLTableColElement;
            if (tr[i].querySelector("input[name^='option[']:checked")) {
                td.removeAttribute('style');
            } else {
                td.style.color = '#FF0000';
            }
        }
    }

    /**
     * @desc Saving the question's response done by patients
     */
    save(template?: TemplateRef<any>) {
        const data = [],
            option_ids = document.querySelectorAll(
                "input[name^='option[']:checked"
            );
        if (
            option_ids.length == this.totalQuestions ||
            this.confirm_submition
        ) {
            for (let i = 0, opt = option_ids, len = opt.length; i < len; i++) {
                const question_id = opt[i].getAttribute("data-question-id");
                const group_id = opt[i].getAttribute("group-id");
                const values = opt[i]["value"];
                const response = opt[i].getAttribute("title");
                data.push({
                    options_id: values,
                    questions_id: question_id,
                    question_groups_id: group_id,
                    week_info_id: this.weekInfoId,
                    response: response
                });
            }
            if (this.confirm_submition && option_ids.length == 0) {
                data.push({
                    question_groups_id: this.groupId,
                    week_info_id: this.weekInfoId,
                });
            }

            // this.questionnaireService
            //     .save_questionnaire(data)
            //     .subscribe(Response => {
            //         if (Response["status"] == "success") {
            //             let path: any;
            //             path =
            //                 this.type == "intolerance"
            //                     ? ["/questionnaire/rumination"]
            //                     : this.type == "rumination"
            //                         ? ["/questionnaire/promis"]
            //                         : this.type == "promis"
            //                             ? ["/questionnaire/fact"]
            //                             : ["/patient/dashboard"];
            //             this.router.navigate(path).then(() => {
            //                 this.toastr.success(Response["msg"]);
            //                 this.confirm_submition = false;
            //                 this.modalRef.hide();
            //             });
            //         } else {
            //             this.toastr.error(Response["msg"]);
            //         }
            //     });
        } else {

            this.openModal(template);
        }
    }
}

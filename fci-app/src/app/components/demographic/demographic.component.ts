import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { QuestionnaireService } from '../../service/questionnaire.service';
import { HelperService } from '../../service/helper.service';
import { DataService } from "../../service/data.service";

@Component({
  selector: 'app-demographic',
  templateUrl: './demographic.component.html',
  styleUrls: ['./demographic.component.css']
})
export class DemographicComponent implements OnInit {
  breadcrumb = [{ link: '/', title: 'Home' }, { title: 'My Details', class: 'active' }];

  questions=[];
  token=localStorage.getItem('token');
  answers:any;
  
  constructor(
    public toastr: ToastrService,
    private questionnaireService: QuestionnaireService,
    private dataService: DataService,
    public router :Router,
    public helper: HelperService)
     { }

  ngOnInit() {
    this.getDemographic();
  }

  getDemographic(){
    // this.modalType='demograhic';
    this.questionnaireService.getDemographic().subscribe(
      response => {
        if (response.status == 'success') {
          this.questions=response['data']['questions'];
          setTimeout(function(){
            this.optionAction();
          }.bind(this),50)
        } 
      },
      error => { }
    );
  }

  saveDemographic(){
    this.answers=[];
    const option = document.querySelectorAll("input[name^='op_']:checked");
    this.getAnswer(option);

    const texts = document.querySelectorAll("input[name^='text_']");
    this.getAnswer(texts);

    const selects = document.querySelectorAll("select[name^='select_']");
    this.getAnswer(selects,true);

    const divs = document.querySelectorAll("div[id^='ques_']");
    
    let isSavable=true,flag=true;
    for(let i=0;i<divs.length;i++){
      let id=divs[i]['id'],qId=id.split('_')[1];
      let isDisabled=divs[i]['disabled'];
      flag=this.answers.some(function(answer) {
        return isDisabled || (answer['response'].trim()!='' && answer['question_id'] == qId);
      });

      let pTag=document.getElementById('error_'+qId);
      pTag.classList.add('hidden');
      
      if(!flag && isSavable){
        isSavable=false;
        this.navigateToElem(qId);
        pTag.classList.remove('hidden');
      }
    }

    isSavable?
    this.questionnaireService.saveDemographic({
        demographic_data: this.answers
    }).subscribe(response => {
        if (response.status == 'success') {
            this.router.navigate(['/patient/module-1/intro']).then(() => {
              this.toastr.success(response.msg, null);
            });
        }else{
          this.toastr.error(response.msg, null);
        }
    }):'';
  }

  getAnswer(obj1,isSelectBox=false) {
    if (!this.helper.isEmptyArr(obj1)) {
      for (let i = 0, len = obj1.length; i < len; i++) {

        let response = null;
        response = obj1[i]["value"];
        let questionId = obj1[i].getAttribute("question-id"),optionId = obj1[i].getAttribute("option-id"), isOther = obj1[i].getAttribute("isOther-id");

        if(isSelectBox){
          let value=response.trim().split("-");
          optionId=value[0];
          response=value[1];
        }
        

        if(isOther=='1'){
          document.getElementById('text_other_'+questionId+'_'+optionId);
          response=(<HTMLInputElement>document.getElementById('text_other_'+questionId+'_'+optionId)).value;

          console.log('>>other',response);
        }

        if(optionId){
          this.answers.push({
              question_id: questionId,
              option_id: optionId,
              response: response
          });
        }
      }
    }
  }

  setRange(item,min=0,max=1){
    if(max==null || max<=0){
      return item;
    }
    item=[];
    for(let i=min;i<=max;i++){
      item.push({'option_value':i,'option_label':i,'id':item['id']});
    }
    return item;
  }

  isChecked(option,question_id){
    if(option!=undefined && option.is_other_option=='1'){
        let input=(<HTMLInputElement>document.getElementById('text_other_'+question_id+'_'+option.id));

        let radio=(<HTMLInputElement>document.getElementById('op_'+question_id+'_'+option.id));

        let flag= (option.response!='' && option.response!=null);
        return flag;
    }
    return option.response!=null && option.response.trim()==option.option_label.trim();
  }

  optionAction(){
    const doms = document.querySelectorAll("input[id^='op_']");

    if (!this.helper.isEmptyArr(doms)) {
      for (let i = 0, len = doms.length; i < len; i++) {
        const questionId = doms[i].getAttribute("question-id"),
        optionId = doms[i].getAttribute("option-id"),
        isOther = doms[i].getAttribute("isOther-id"),
        dependentId = doms[i].getAttribute("dependentQues-id"),
        response = doms[i].getAttribute("response");

        let checked=doms[i]["checked"];
        if(isOther=='1'){
          let input=(<HTMLInputElement>document.getElementById('text_other_'+questionId+'_'+optionId));

          input.disabled=!checked;

          console.log('>>',response);
          !checked?input.value='':(response && response!=undefined && response!=null?input.value=response:'');
        }


        if(dependentId!=null && parseInt(dependentId)>0){
          let disOps=document.querySelectorAll("input[id^='op_"+dependentId+"']");
          
          const deptQues=(<HTMLInputElement>document.getElementById("ques_"+dependentId+""));

           deptQues.disabled=!checked
          for(let i=0;i<disOps.length;i++){
              let qId = disOps[i].getAttribute("question-id");
              let opId = disOps[i].getAttribute("option-id");
              const opt=(<HTMLInputElement>document.getElementById("op_"+qId+"_"+opId+""));

              opt.disabled=!checked;
              !checked?(opt.checked=false):'';
          }
        }

      }
    }
  }

  navigateToElem(scrollId: any = 0) {

    console.log('>>scro',scrollId);
    if (scrollId) {
      setTimeout(() => {
        let b = document.getElementById('ques_' + scrollId);
        if (b) b.scrollIntoView({ behavior: "smooth", block: "center" });
      }, 100);

    } else {
      this.dataService.currentMessage.subscribe(() => {
        window.scrollTo({
          top: 0,
          left: 0,
          behavior: "smooth"
        });
      });
    }
  }

}

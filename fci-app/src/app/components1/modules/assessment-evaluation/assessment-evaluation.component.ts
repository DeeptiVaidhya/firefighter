import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-assessment-evaluation',
  templateUrl: './assessment-evaluation.component.html',
  styleUrls: ['./assessment-evaluation.component.css']
})
export class AssessmentEvaluationComponent implements OnInit {

  module = 'module-1';
  title = 'Module 1';

  introModules = {
    'module-1': { title: 'Module 1', text1: 'Cancer in the Fire Service', text2: 'Assessment and Evaluation' },
    'module-2': { title: 'Module 2', text1: 'Firefighter<br> Occupational Cancer Risk', text2: 'Assessment and Evaluation' },
    'module-3': { title: 'Module 3', text1: 'Reducing Cancer<br> Risk in the Fire<br> Service', text2: 'Assessment and Evaluation' },
    'module-4': { title: 'Module 4', text1: 'Organizational Level<br> Change in the Fire<br> Service', text2: 'Assessment and Evaluation' },
    'module-5': { title: 'Module 5', text1: 'Ongoing Learning:<br> Resources', text2: 'Assessment and Evaluation' },
  };


  public constructor(private route: ActivatedRoute, private router: Router) {
    this.module = route.snapshot.paramMap.get("module");
    this.title = this.module.replace(/[\-]/g, ' ');
    if (this.title) {
      this.title = this.title.charAt(0).toUpperCase() + this.title.slice(1);
    }
  }

  startAssEva(type: any) {
    let url = '';

    switch (type) {
      case 'module-1': {
        url = '/patient/module-1/question/1';
        break;
      }
      case 'module-2': {
        url = '/patient/module-2/question/1'
        break;
      }
      case 'module-3': {
        url = '/patient/module-3/question/1'
        break;
      }
      case 'module-4': {
        url = '/patient/module-4/question/1'
        break;
      }
    }

    console.log(url);

    this.router.navigate([url]).catch((e) => {
      console.log('Url not exist!');
    });
  }

  ngOnInit() {
  }

}

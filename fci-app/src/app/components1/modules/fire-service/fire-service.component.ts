import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-fire-service',
  templateUrl: './fire-service.component.html',
  styleUrls: ['./fire-service.component.css']
})

export class FireServiceComponent implements OnInit {

  module = 'module-3';
  title = 'Module 3';
  index: any = 0;
  // introModules = {
  //   'module-1': { title: 'Module 1', text1: 'Cancer in the Fire Service', text2: 'Assessment and Evaluation' },
  //   'module-2': { title: 'Module 2', text1: 'Firefighter<br> Occupational Cancer Risk', text2: 'Assessment and Evaluation' },
  //   'module-3': { title: 'Module 3', text1: 'Reducing Cancer<br> Risk in the Fire<br> Service', text2: 'Assessment and Evaluation' },
  //   'module-4': { title: 'Module 4', text1: 'Organizational Level<br> Change in the Fire<br> Service', text2: 'Assessment and Evaluation' },
  //   'module-5': { title: 'Module 5', text1: 'Ongoing Learning:<br> Resources', text2: 'Assessment and Evaluation' },
  // };

  public constructor(private route: ActivatedRoute, private router: Router) {
    this.module = route.snapshot.paramMap.get("module");
    this.index = route.snapshot.paramMap.get("index");
    this.title = this.module.replace(/[\-]/g, ' ');    
    if (this.title) {
      this.title = this.title.charAt(0).toUpperCase() + this.title.slice(1);
    }
  }

  ngOnInit() {
  }

}

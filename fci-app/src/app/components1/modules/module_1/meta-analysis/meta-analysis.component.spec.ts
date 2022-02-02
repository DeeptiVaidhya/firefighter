import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MetaAnalysisComponent } from './meta-analysis.component';

describe('MetaAnalysisComponent', () => {
  let component: MetaAnalysisComponent;
  let fixture: ComponentFixture<MetaAnalysisComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MetaAnalysisComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MetaAnalysisComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

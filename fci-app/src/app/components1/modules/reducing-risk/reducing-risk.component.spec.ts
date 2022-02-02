import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ReducingRiskComponent } from './reducing-risk.component';

describe('ReducingRiskComponent', () => {
  let component: ReducingRiskComponent;
  let fixture: ComponentFixture<ReducingRiskComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ReducingRiskComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ReducingRiskComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

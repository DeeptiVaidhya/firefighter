import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ScopeOfProblemComponent } from './scope-of-problem.component';

describe('ScopeOfProblemComponent', () => {
  let component: ScopeOfProblemComponent;
  let fixture: ComponentFixture<ScopeOfProblemComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ScopeOfProblemComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ScopeOfProblemComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

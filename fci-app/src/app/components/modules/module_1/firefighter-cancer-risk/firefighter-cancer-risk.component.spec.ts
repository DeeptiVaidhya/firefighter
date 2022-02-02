import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FirefighterCancerRiskComponent } from './firefighter-cancer-risk.component';

describe('FirefighterCancerRiskComponent', () => {
  let component: FirefighterCancerRiskComponent;
  let fixture: ComponentFixture<FirefighterCancerRiskComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FirefighterCancerRiskComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FirefighterCancerRiskComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

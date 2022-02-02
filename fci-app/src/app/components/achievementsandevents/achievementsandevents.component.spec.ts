import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AchievementsandeventsComponent } from './achievementsandevents.component';

describe('AchievementsandeventsComponent', () => {
  let component: AchievementsandeventsComponent;
  let fixture: ComponentFixture<AchievementsandeventsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AchievementsandeventsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AchievementsandeventsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

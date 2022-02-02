import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ToxinsFromComponent } from './toxins-from.component';

describe('ToxinsFromComponent', () => {
  let component: ToxinsFromComponent;
  let fixture: ComponentFixture<ToxinsFromComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ToxinsFromComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ToxinsFromComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NioshComponent } from './niosh.component';

describe('NioshComponent', () => {
  let component: NioshComponent;
  let fixture: ComponentFixture<NioshComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NioshComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NioshComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { Component, OnInit } from '@angular/core';

@Component({
	selector: 'app-references',
	templateUrl: './references.component.html',
	styleUrls: ['./references.component.css']
})
export class ReferencesComponent implements OnInit {

	references = [
		{'text_normal':'American Cancer Society. (2015, July 27).','text_bold':'Diesel Exhaust and Cancer.'},
		{'text_normal':'American Cancer Society. (2017, April 14).','text_bold':'Diet and Physical Activity: What’s the Cancer Connection?'},
		{'text_normal':'American Chemical Society. (2017, October 18).','text_bold':'Battling flames increases firefighters\' exposure to carcinogens'},
		{'text_normal':'Anderson, D. A., Harrison, T. R., Yang, F., Wendorf Muhamad, J., & Morgan, S. E. (2017).','text_bold':'Firefighter perceptions of cancer risk: Results of a qualitative study'},
		{'text_normal':'Baldwin, T.N., Hales, T.R., Niemeier, M.T. (2011, February 1).','text_bold':'«Controlling Diesel Exhaust Exposure Inside Firehouses.» Fire Engineering.'},
		{'text_normal':'Berger, P.S., Moulin, G. (2016).','text_bold':'«Cancer in the Fire Service - A Public Policy Risk Analysis.» Speaking of Fire: Cancer in the Fire Service.'},
		{'text_normal':'Continental Birbau, Inc. (2018, February 20). ','text_bold':'Continental Gear Washers/Dryers Often Qualify for AFG Grants'},
		{'text_normal':'For the list above and more ideas, see Controlling','text_bold':'Diesel Exhaust Exposure Inside Firehouses publication'}
	];
	constructor() { }

	ngOnInit() {
	}

}

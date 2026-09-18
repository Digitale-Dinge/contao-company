import './styles/opening-times.pcss';

import { OpeningStatus } from './scripts/opening-status';

if (!window.CompanyOpeningStatus) {
    window.CompanyOpeningStatus = OpeningStatus;
    new OpeningStatus();
}

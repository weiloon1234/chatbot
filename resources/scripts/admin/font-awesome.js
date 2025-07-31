import { library } from '@fortawesome/fontawesome-svg-core';
import { faTrashAlt } from '@fortawesome/free-regular-svg-icons';
import {
    faCalendar, faCheck, faClock, faClose, faCopy, faEye, faEyeSlash,
    faInfoCircle, faKey, faCaretDown, faCaretUp, faChevronRight,
    faChevronLeft, faAngleRight, faAnglesRight, faAngleLeft, faAnglesLeft,
    faDownload, faPlus, faPlusCircle, faMinus, faMinusCircle, faTimes,
    faTimesCircle, faDivide, faPencil, faTrash, faSpinner, faHome, faUser,
    faBook, faUserSecret, faCogs,
} from "@fortawesome/free-solid-svg-icons";

const solidIcons = [
    faCalendar, faCheck, faClock, faClose, faCopy, faEye, faEyeSlash,
    faInfoCircle, faKey, faCaretDown, faCaretUp, faChevronRight,
    faChevronLeft, faAngleRight, faAnglesRight, faAngleLeft, faAnglesLeft,
    faDownload, faPlus, faPlusCircle, faMinus, faMinusCircle, faTimes,
    faTimesCircle, faDivide, faPencil, faTrash, faSpinner, faHome, faUser,
    faBook, faUserSecret, faCogs,
];

library.add(...solidIcons, faTrashAlt);

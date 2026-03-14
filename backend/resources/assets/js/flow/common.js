import type { PermissionTypes } from 'auth-permission-types';
import {
    CONTACT_TYPES,
    AUTH_GUARDS,
    DOCTYPES
} from '../support/constants';


const { USER, COMPANY, EMPLOYEE } = CONTACT_TYPES;

declare type StateResource = {
    current: string,
    completedStates: Array<string>,
    completionRate: number,
    transitionableStates: Array<string>
}

declare type BaseModelProps = {
    id?: ?number,
    createdAt?: ?string,
    updatedAt?: ?string,
    alias?: ?string,
}

declare type AuthStatus = 'online' | 'offline';
declare type VerificationStatus = 'VERIFIED' | 'UNVERIFIED';
declare type ContactableType = USER | COMPANY | EMPLOYEE;
declare type Guard = AUTH_GUARDS.USER | AUTH_GUARDS.COMPANY;
declare type DocType = DOCTYPES.ORDER | DOCTYPES.INVOICE | DOCTYPES.DELIVERY | DOCTYPES.TASK;
declare type Authenticatable = User | Company;
declare type Authorizable = User | Employee;
declare type InvitationType = 'contact' | 'employment';

declare type ImageType = ImageSVG | ImageJPEG | ImagePNG;
declare type VideoType = VideoMPEG | VideoMSVID | Video3GP | VideoMP4;
declare type MimeType = ImageType | VideoType;

declare type ImageSVG = 'image/svg+xml';
declare type ImageJPEG = 'image/jpeg';
declare type ImagePNG = 'image/png';

declare type VideoMPEG = 'video/mpeg';
declare type VideoMSVID = 'video/x-msvideo';
declare type Video3GP = 'video/3gp';
declare type VideoMP4 = 'video/mp4';

declare type ActionPayload = {
    loader?: boolean
}
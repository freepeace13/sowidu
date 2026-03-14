import Vue from 'vue'

import RootView from '~/views/Layouts/RootView'

import ModalLayout from '~/components/layouts/Modal'

// Input form fields
import TextField from '~/components/UI/Inputs/TextField'
import TextAreaField from '~/components/UI/Inputs/TextAreaField'
import DatePicker from '~/components/UI/Inputs/DatePicker'
import TimePicker from '~/components/UI/Inputs/TimePicker'
import AvatarCropper from '~/components/UI/Inputs/AvatarCropper'

// Thumbnail and list item
// import CatalogueCard from '~/components/UI/CatalogueCard'
//import ItemCard from '~/components/UI/ItemCard'
// import ContactCard from '~/components/UI/ContactCard'
import ImageSlider from '~/components/UI/ImageSlider'
// import OrderCard from '~/components/UI/OrderCard'
// import TaskCard from '~/components/UI/TaskCard'
import BackButton from '~/components/UI/BackButton'
import UserIcon from '~/components/UI/UserIcon'
import OrderStatusBadge from '~/components/UI/OrderStatusBadge'
// import DeliveryCard from '~/components/UI/DeliveryCard'
import DocumentThumbnail from '~/components/UI/DocumentThumbnail'

// import CustomerInfoPane from '~/components/UI/CustomerInfoPane'
// import ContractorInfoPane from '~/components/UI/ContractorInfoPane'

// Input form selectors
import EmployeeSelector from '~/components/UI/Inputs/Selectors/Employee'
import CitySelector from '~/components/UI/Inputs/Selectors/City'
import StateSelector from '~/components/UI/Inputs/Selectors/State'
import StreetSelector from '~/components/UI/Inputs/Selectors/Street'
import CountrySelector from '~/components/UI/Inputs/Selectors/Country'
import ZipcodeSelector from '~/components/UI/Inputs/Selectors/Zipcode'
import HouseNumberSelector from '~/components/UI/Inputs/Selectors/HouseNumber'
import LegalFormSelector from '~/components/UI/Inputs/Selectors/LegalForm'
import InstitutionTypeSelector from '~/components/UI/Inputs/Selectors/InstitutionType'
import SpecializationSelector from '~/components/UI/Inputs/Selectors/Specialization'
import CustomerSelector from '@features/account/components/CustomerSelector';
// import ItemTypeSelector from '~/components/UI/Inputs/Selectors/ItemType'
// import UnitSelector from '~/components/UI/Inputs/Selectors/Unit'
import LoaderButton from '~/components/pagination/LoaderButton'
import EmptyMessage from '~/components/common/EmptyMessage'
import Tab from '~/components/common/Tab'

Vue.component('ModalLayout', ModalLayout)

// Pagination
Vue.component('LoaderButton', LoaderButton)

// Page layout components
Vue.component('RootView', RootView)

// Vue.component('CustomerInfoPane', CustomerInfoPane)
// Vue.component('ContractorInfoPane', ContractorInfoPane)

/**
 * Common
 */
// Vue.component('DeliveryCard', DeliveryCard)
// Vue.component('CatalogueCard', CatalogueCard)
//Vue.component('ItemCard', ItemCard)
// Vue.component('ContactCard', ContactCard)
Vue.component('ImageSlider', ImageSlider)
// Vue.component('OrderCard', OrderCard)
// Vue.component('TaskCard', TaskCard)
Vue.component('BackButton', BackButton)
Vue.component('UserIcon', UserIcon)
Vue.component('EmptyMessage', EmptyMessage)
Vue.component('Tab', Tab)

Vue.component('DocumentThumbnail', DocumentThumbnail)

/**
 * Order
 */
Vue.component('OrderStatusBadge', OrderStatusBadge)

/**
 * Inputs
 */
Vue.component('AvatarCropper', AvatarCropper)
Vue.component('TextField', TextField)
Vue.component('TextAreaField', TextAreaField)
Vue.component('DatePicker', DatePicker)
Vue.component('TimePicker', TimePicker)

/**
 * Selectors
 */
Vue.component('EmployeeSelector', EmployeeSelector)
Vue.component('CitySelector', CitySelector)
Vue.component('StateSelector', StateSelector)
Vue.component('StreetSelector', StreetSelector)
Vue.component('CountrySelector', CountrySelector)
Vue.component('ZipcodeSelector', ZipcodeSelector)
Vue.component('HouseNumberSelector', HouseNumberSelector)
Vue.component('LegalFormSelector', LegalFormSelector)
Vue.component('InstitutionTypeSelector', InstitutionTypeSelector)
Vue.component('SpecializationSelector', SpecializationSelector)
Vue.component('CustomerSelector', CustomerSelector)
// Vue.component('ItemTypeSelector', ItemTypeSelector)
// Vue.component('UnitSelector', UnitSelector)

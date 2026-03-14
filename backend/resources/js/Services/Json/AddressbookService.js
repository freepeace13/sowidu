import AddressbookService from '@/Apps/Addressbook/Services/AddressbookService'
import OrganizationService from '@/Apps/Addressbook/Services/OrganizationService'
import PersonService from '@/Apps/Addressbook/Services/PersonService'

/** @deprecated */
AddressbookService.appendPrototype = function (src) {
    for (const prop in src.prototype) {
        this.prototype[prop] = src.prototype[prop]
    }
}

AddressbookService.appendPrototype(PersonService)
AddressbookService.appendPrototype(OrganizationService)

export default AddressbookService

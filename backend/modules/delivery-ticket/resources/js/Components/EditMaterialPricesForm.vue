<script setup>
// Importiert das Submit-Button-Component
// Imports the submit button component
import SubmitButton from '@/Components/Forms/SubmitButton.vue'

// Importiert globale Helfer (z.B. Übersetzungen, Routen)
// Imports global helpers (e.g. translations, routes)
import useGlobalVariables from '@/Composables/useGlobalVariables'

// useForm für komfortables Arbeiten mit Formularen (Inertia.js)
// useForm for convenient form handling (Inertia.js)
import { useForm } from '@inertiajs/vue2'

// ref für reaktive Werte (z.B. Dialog sichtbar, aktuelles Material)
// ref for reactive values (e.g. dialog visibility, current material)
import { ref } from 'vue'

// Stellt die Funktion `show` nach außen bereit, damit der Eltern-Component
// dieses Formular/Modal öffnen kann.
// Exposes the `show` function so the parent component can open this form/modal.
defineExpose({ show })

// Übergabe-Props: Das aktuelle Lieferschein-Objekt
// Props: The current delivery ticket object
const props = defineProps({
    deliveryTicket: {
        type: Object,
        required: true,
    },
})

// Globale Variablen/Funktionen: $t (Übersetzung), $route (Routen-Helfer)
// Global variables/functions: $t (translation), $route (route helper)
const { $t, $route } = useGlobalVariables()

// Steuert, ob der Dialog sichtbar ist
// Controls whether the dialog is visible
const isShow = ref(false)

// Hält das aktuell ausgewählte Material, dessen Preise bearbeitet werden
// Holds the currently selected material whose prices are being edited
const material = ref(null)

// Formularzustand für Einkaufs- und Verkaufspreis
// Form state for purchasing and selling prices
const form = useForm({
    purchasing_price: null,
    selling_price: null,
})

/**
 * Öffnet das Modal und lädt die Werte des übergebenen Material-Items.
 * If no item is passed, nothing happens.
 *
 * @param {Object} item - Das Material, dessen Preise bearbeitet werden.
 * @param {Object} item - The material whose prices are edited.
 */
function show(item) {
    if (!item) return

    // Material setzen
    // Set current material
    material.value = item

    // Formularwerte aus dem Material übernehmen
    // Populate form values from the material
    form.purchasing_price = item.purchasing_price
    form.selling_price = item.selling_price

    // Dialog anzeigen
    // Show dialog
    isShow.value = true
}

/**
 * Schließt das Modal und setzt Zustand und Formular zurück.
 * Closes the modal and resets state and form.
 */
function close() {
    // Dialog ausblenden
    // Hide dialog
    isShow.value = false

    // Aktuelles Material zurücksetzen
    // Reset current material
    material.value = null

    // Formular auf Initialwerte zurücksetzen
    // Reset form to initial values
    form.reset()
}

/**
 * Sendet das Formular an den Server und aktualisiert die Materialpreise.
 * Sends the form to the server and updates the material prices.
 */
function submit() {
    const deliveryTicket = props.deliveryTicket

    // Zusätzliche Daten (Menge) hinzufügen und Patch-Request senden
    // Add additional data (quantity) and send patch request
    form.transform((data) => ({
        ...data,
        quantity: material.value.quantity,
    })).patch(
        $route('delivery_tickets.materials.update', {
            deliveryTicket,
            material: material.value,
        }),
        {
            // Zustand der Seite beibehalten (kein kompletter Reload)
            // Preserve page state (no full reload)
            preserveState: true,

            // Scrollposition beibehalten
            // Preserve scroll position
            preserveScroll: true,

            // Nur bestimmte Props neu laden (Materialien + Summen)
            // Only reload specific props (materials + totals)
            only: ['materials', 'totals'],

            // Bei Erfolg Dialog schließen
            // On success, close the dialog
            onSuccess: () => {
                close()
            },
        },
    )
}
</script>

<template>
    <!-- Dialog für das Bearbeiten der Materialpreise -->
    <!-- Dialog for editing material prices -->
    <v-dialog
        v-model="isShow"
        max-width="400"
    >
        <v-card>
            <!-- Titel des Modals -->
            <!-- Modal title -->
            <v-card-title class="title">
                {{ $t('delivery_tickets.form.update-delivery-prices') }}
            </v-card-title>

            <!-- Inhalt des Modals -->
            <!-- Modal content -->
            <v-card-text>
                <VContainer
                    fluid
                    pa-0
                    grid-list-lg
                >
                    <v-layout column>
                        <!-- Eingabefeld: Einkaufspreis -->
                        <!-- Input field: Purchasing price -->
                        <!--
                            WICHTIG / IMPORTANT:
                            Erlaubt die Eingabe mit Komma (z.B. 25,42), indem
                            beim Tippen automatisch jedes Komma in einen Punkt
                            umgewandelt wird. So akzeptiert das System
                            deutsche Eingabe, speichert aber ein gültiges
                            Dezimalformat für den Server.

                            Allows comma input (e.g. 25,42) by automatically
                            converting each comma to a dot while typing.
                            This way the system accepts German-style input
                            but sends a valid decimal format to the server.
                        -->
                        <v-flex xs12>
                            <v-text-field
                                v-model="form.purchasing_price"
                                outline
                                full-width
                                :items="[]"
                                :loading="form.processing"
                                :error-messages="form.errors.purchasing_price"
                                :hide-details="!form.errors.purchasing_price"
                                :label="
                                    $t(
                                        'delivery_tickets.labels.purchasing-price',
                                    )
                                "
                                class="required-input"
                                required
                                @input="
                                    form.purchasing_price = (
                                        $event || ''
                                    ).replace(',', '.')
                                "
                            />
                        </v-flex>

                        <!-- Eingabefeld: Verkaufspreis -->
                        <!-- Input field: Selling price -->
                        <!--
                            Siehe Kommentar oben:
                            Auch hier werden Kommas in Punkte gewandelt,
                            damit Eingaben wie 38,97 problemlos akzeptiert
                            und korrekt verarbeitet werden.

                            See comment above:
                            Here, commas are also converted to dots so that
                            inputs like 38,97 are accepted and processed correctly.
                        -->
                        <v-flex xs12>
                            <v-text-field
                                v-model="form.selling_price"
                                outline
                                full-width
                                :items="[]"
                                :loading="form.processing"
                                :error-messages="form.errors.selling_price"
                                :hide-details="!form.errors.selling_price"
                                :label="
                                    $t('delivery_tickets.labels.selling-price')
                                "
                                class="required-input"
                                required
                                @input="
                                    form.selling_price = ($event || '').replace(
                                        ',',
                                        '.',
                                    )
                                "
                            />
                        </v-flex>
                    </v-layout>
                </VContainer>
            </v-card-text>

            <!-- Aktionen im Footer: Schließen & Speichern -->
            <!-- Footer actions: Close & Save -->
            <v-card-actions>
                <v-spacer />
                <v-btn @click="close">
                    {{ $t('buttons.close') }}
                </v-btn>
                <SubmitButton
                    :is-processing="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.save') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

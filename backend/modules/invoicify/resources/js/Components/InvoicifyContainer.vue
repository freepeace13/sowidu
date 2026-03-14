<template>
    <div id="invoicify-container">
        <slot />

        <portal-target name="preview-dialog" />
        <portal-target name="manual-item-dialog" />
        <portal-target name="edit-pdf-properties-dialog" />
        <portal-target name="edit-invoice-details-dialog" />
    </div>
</template>

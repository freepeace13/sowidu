<template>
    <v-layout row>
        <v-flex xs2>
            <slot name="sidebar" />
        </v-flex>
        <v-flex xs10>
            <slot />
        </v-flex>
    </v-layout>
</template>
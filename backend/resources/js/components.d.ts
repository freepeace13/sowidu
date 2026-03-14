import type {
    DataTableHeader,
    DataOptions,
    CalendarTimestamp as VTimestamp,
} from 'vuetify'
import type VueComponent from 'vue'
import type { DefineComponent, VNode } from 'vue'
import type { VBtn } from 'vuetify2-component-types'

type eventHandler = Function
interface srcObject {
    src: string
    srcset?: string
    lazySrc: string
    aspect: number
}

export type InputValidationRule = (value: any) => string | boolean
// We define our own InputValidationRules because vuetify incorrectly does not include
// boolean as a valid array member in its definition of InputValidationRules.
export type InputValidationRules = (InputValidationRule | string | boolean)[]

declare module 'vue' {
    export interface GlobalComponents {
        SubmitButton: typeof import('vuetify/lib/components/VBtn/VBtn').default
        VLayout: DefineComponent<
            {
                /** Applies the [align-items](https://developer.mozilla.org/en-US/docs/Web/CSS/align-items) css property. Available options are **start**, **center**, **end**, **baseline** and **stretch**. */
                align?: string | null
                /** Applies the [align-content](https://developer.mozilla.org/en-US/docs/Web/CSS/align-content) css property. Available options are **start**, **center**, **end**, **space-between**, **space-around** and **stretch**. */
                alignContent?: string | null
                /** Changes the **align-content** property on large and greater breakpoints. */
                alignContentLg?: string | null
                /** Changes the **align-content** property on medium and greater breakpoints. */
                alignContentMd?: string | null
                /** Changes the **align-content** property on small and greater breakpoints. */
                alignContentSm?: string | null
                /** Changes the **align-content** property on extra large and greater breakpoints. */
                alignContentXl?: string | null
                /** Changes the **align-items** property on large and greater breakpoints. */
                alignLg?: string | null
                /** Changes the **align-items** property on medium and greater breakpoints. */
                alignMd?: string | null
                /** Changes the **align-items** property on small and greater breakpoints. */
                alignSm?: string | null
                /** Changes the **align-items** property on extra large and greater breakpoints. */
                alignXl?: string | null
                /** Reduces the gutter between `v-col`s. */
                dense?: boolean | null
                /** Applies the [justify-content](https://developer.mozilla.org/en-US/docs/Web/CSS/justify-content) css property. Available options are **start**, **center**, **end**, **space-between** and **space-around**. */
                justify?: string | null
                /** Changes the **justify-content** property on large and greater breakpoints. */
                justifyLg?: string | null
                /** Changes the **justify-content** property on medium and greater breakpoints. */
                justifyMd?: string | null
                /** Changes the **justify-content** property on small and greater breakpoints. */
                justifySm?: string | null
                /** Changes the **justify-content** property on extra large and greater breakpoints. */
                justifyXl?: string | null
                /** Removes the gutter between `v-col`s. */
                noGutters?: boolean | null
                /** Specify a custom tag used on the root element. */
                tag?: string | null
            },
            {
                $scopedSlots: Readonly<{
                    /** The default Vue slot. */
                    default: undefined
                }>
            }
        >

        VFlex: DefineComponent<
            {
                /** Applies the [align-items](https://developer.mozilla.org/en-US/docs/Web/CSS/align-items) css property. Available options are **start**, **center**, **end**, **auto**, **baseline** and **stretch**. */
                alignSelf?: string | null
                /** Sets the default number of columns the component extends. Available options are **1 -> 12** and **auto**. */
                cols?: boolean | string | number | null
                /** Changes the number of columns on large and greater breakpoints. */
                lg?: boolean | string | number | null
                /** Changes the number of columns on medium and greater breakpoints. */
                md?: boolean | string | number | null
                /** Sets the default offset for the column. */
                offset?: string | number | null
                /** Changes the offset of the component on large and greater breakpoints. */
                offsetLg?: string | number | null
                /** Changes the offset of the component on medium and greater breakpoints. */
                offsetMd?: string | number | null
                /** Changes the offset of the component on small and greater breakpoints. */
                offsetSm?: string | number | null
                /** Changes the offset of the component on extra large and greater breakpoints. */
                offsetXl?: string | number | null
                /** Sets the default [order](https://developer.mozilla.org/en-US/docs/Web/CSS/order) for the column. */
                order?: string | number | null
                /** Changes the order of the component on large and greater breakpoints. */
                orderLg?: string | number | null
                /** Changes the order of the component on medium and greater breakpoints. */
                orderMd?: string | number | null
                /** Changes the order of the component on small and greater breakpoints. */
                orderSm?: string | number | null
                /** Changes the order of the component on extra large and greater breakpoints. */
                orderXl?: string | number | null
                /** Changes the number of columns on small and greater breakpoints. */
                sm?: boolean | string | number | null
                /** Specify a custom tag used on the root element. */
                tag?: string | null
                /** Changes the number of columns on extra large and greater breakpoints. */
                xl?: boolean | string | number | null
            },
            {
                $scopedSlots: Readonly<{
                    /** The default Vue slot. */
                    default: undefined
                }>
            }
        >
    }
}

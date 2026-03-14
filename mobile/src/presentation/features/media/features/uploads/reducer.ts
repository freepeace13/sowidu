import { createReducer } from "@reduxjs/toolkit"

import { uploadCreated, progressReceived, uploadCompleted } from "./actions"

export interface UploadProgress {
  totalBytesSent: number
  totalBytesExpectedToSend: number
}

export interface UploadEntity {
  uuid: string
  uri: string
  timestamp: number
  progress?: UploadProgress
}

export interface UploadsState {
  entities: UploadEntity[]
}

export const initialState: UploadsState = {
  entities: [],
}

export const uploadsReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(uploadCreated, (state, { payload }) => {
      state.entities.push({
        ...payload,
        progress: {
          totalBytesSent: 0,
          totalBytesExpectedToSend: 1,
        },
      })
    })
    .addCase(progressReceived, (state, { payload }) => {
      const index = state.entities.findIndex((entity) => entity.uuid === payload.uuid)

      if (index !== -1) {
        const { totalBytesExpectedToSend, totalBytesSent } = payload.progress

        const updatedEntity = {
          ...state.entities[index],
          progress: {
            totalBytesSent,
            totalBytesExpectedToSend,
          },
        }

        state.entities.splice(index, 1, updatedEntity)
      }
    })
    .addCase(uploadCompleted, (state, action) => {
      const index = state.entities.findIndex((entity) => entity.uuid === action.payload.uuid)

      if (index !== -1) {
        state.entities.splice(index, 1)
      }
    })
})

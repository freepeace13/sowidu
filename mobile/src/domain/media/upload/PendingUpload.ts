export type UploadId = string
type Timestamp = number

export class PendingUpload {
  public cancelledAt?: Timestamp

  public constructor(
    public readonly uuid: UploadId,
    public readonly promise: Promise<any>,
    private readonly _cancelAsync: () => Promise<void>,
    public readonly createdAt: Timestamp = Date.now()
  ) {}

  public async cancel(): Promise<void> {
    await this._cancelAsync()
    this.cancelledAt = new Date().getTime()
  }
}

export class UploadProgress {
  public constructor(
    public readonly totalBytesSent: number,
    public readonly totalBytesExpectedToSend: number
  ) {}

  public percentage(): number {
    const totalBytesSent = Math.max(this.totalBytesSent, 0)
    const totalBytesExpectedToSend = Math.max(this.totalBytesExpectedToSend, 1)

    return Math.floor((totalBytesSent / totalBytesExpectedToSend) * 100)
  }
}

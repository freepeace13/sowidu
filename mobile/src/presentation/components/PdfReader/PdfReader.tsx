import Pdf, { PdfProps, Source as PdfSource } from "react-native-pdf"

import Style from "./PdfReaderStyle"

interface Props {
  source: PdfSource
  onError?: PdfProps["onError"]
}

function PdfReader(props: Props) {
  return (
    <Pdf
      source={{ ...props.source, cache: true }}
      trustAllCerts={false}
      onLoadComplete={console.log}
      onPageChanged={console.log}
      onError={props.onError}
      onPressLink={console.log}
      style={Style.pdf}
    />
  )
}

export default PdfReader

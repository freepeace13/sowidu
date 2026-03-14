import { StyleSheet, Dimensions } from "react-native"

const WindowSize = Dimensions.get("window")

export default StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  background: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: "transparent",
  },
  backgroundViewWrapper: {
    ...StyleSheet.absoluteFillObject,
    alignItems: "center",
    justifyContent: "center",
  },
  contentWrapper: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  overlayWrapper: {
    ...StyleSheet.absoluteFillObject,
    zIndex: 1,
  },
  overlay: {
    justifyContent: "space-between",
    flex: 1,
    width: "100%",
  },
  animatedBackgroundView: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: "rgba(0, 0, 0, .3)",
  },
  headerFullscreen: {
    width: "100%",
    position: "absolute",
  },
  playbackControlsContainer: {
    flexDirection: "row",
    flexShrink: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  playbackControlsButton: {
    marginVertical: 0,
  },
  playbackControlPlayPause: {
    marginHorizontal: 8,
  },
  controls: {
    flexDirection: "column",
    alignItems: "center",
    justifyContent: "space-between",
    paddingHorizontal: 16,
    paddingVertical: 18,
  },
  controlsFullscreen: {
    position: "absolute",
    bottom: 0,
    width: "100%",
  },
  player: {
    height: "100%",
    alignSelf: "stretch",
  },
  video: {
    zIndex: 1,
    aspectRatio: 9 / 16,
  },
  duration: {
    // borderWidth: 1,
    // borderColor: "white",
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    columnGap: 6,
  },
  slider: {
    height: 10,
    width: "100%",
    backgroundColor: "blue",
  },
  videoPlayer: {
    width: "100%",
    height: "100%",
    backgroundColor: "#000",
    zIndex: -1,
  },
  spinnerContainer: {
    ...StyleSheet.absoluteFillObject,
    alignItems: "center",
    justifyContent: "center",
  },
  errorContainer: {
    alignItems: "center",
    justifyContent: "center",
  },
  spinner: {
    backgroundColor: "rgba(0, 0, 0, .2)",
    padding: 4,
    borderRadius: 100,
  },
})

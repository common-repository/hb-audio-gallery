class ClassWatcher {

    constructor(targetNode, classToWatch, classAddedCallback) {
        this.targetNode = targetNode
        this.classToWatch = classToWatch
        this.classAddedCallback = classAddedCallback
        this.observer = null
        this.lastClassState = targetNode.classList.contains(this.classToWatch)

        this.init()
    }

    init() {
        this.observer = new MutationObserver(this.mutationCallback)
        this.observe()
    }

    observe() {
        this.observer.observe(this.targetNode, { attributes: true })
    }

    disconnect() {
        this.observer.disconnect()
    }

    mutationCallback = mutationsList => {
        for (let mutation of mutationsList) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                let currentClassState = mutation.target.classList.contains(this.classToWatch)
                if (this.lastClassState !== currentClassState) {
                    this.lastClassState = currentClassState
                    if (currentClassState) {
                        if (typeof mutation.target.nextSibling.getAttribute === "function" && mutation.target.nextSibling.getAttribute("data-aid")) {
                          this.classAddedCallback(
                            mutation.target.nextSibling.getAttribute("data-aid")
                          );
                        } else {
                          this.classAddedCallback(
                            mutation.target.nextSibling.nextSibling.getAttribute(
                              "data-aid"
                            )
                          );
                        };
                    }
                }
            }
        }
    }
}
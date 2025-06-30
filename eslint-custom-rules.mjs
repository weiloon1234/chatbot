export const noTsInScriptSetup = {
    meta: {
        type: "suggestion",
        fixable: "code",
        docs: {
            description: "Remove lang='ts' from <script setup> tags",
        },
    },
    create: (context) => {
        return {
            Program(node) {
                const sourceCode = context.sourceCode;
                const scriptSetupNode = node.templateBody?.parent?.children.find(
                    (n) =>
                        n.type === "VElement" &&
                        n.name === "script" &&
                        n.startTag.attributes.some(attr => attr.key.name === "setup")
                );

                if (scriptSetupNode) {
                    const langAttr = scriptSetupNode.startTag.attributes.find(
                        (attr) => attr.key.name === "lang"
                    );

                    if (langAttr?.value?.value === "ts") {
                        context.report({
                            node: langAttr,
                            message: "Remove lang='ts' from <script setup>",
                            fix(fixer) {
                                const range = [
                                    langAttr.range[0] - (langAttr.loc.start.column > 0 ? 1 : 0),
                                    langAttr.range[1]
                                ];
                                return fixer.removeRange(range);
                            },
                        });
                    }
                }
            },
        };
    }
};

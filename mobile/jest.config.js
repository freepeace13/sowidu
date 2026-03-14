module.exports = {
  preset: 'ts-jest',
  rootDir: ".",
  roots: ["./src", "./hexagonal"],
  moduleNameMapper: {
    "@infrastructure/(.*)": "<rootDir>/hexagonal/infrastructure/$1",
    "@domain/(.*)": "<rootDir>/hexagonal/domain/$1",
    "@application/(.*)": "<rootDir>/hexagonal/application/$1",
  },
  testEnvironment: 'node',
  testMatch: ['**/__tests__/**/*.ts?(x)', '**/?(*.)+(test).ts?(x)'],
  testPathIgnorePatterns: ['/node_modules/'],
  transform: {
    '^.+\\.tsx?$': ['ts-jest', {//the content you'd placed at "global"
      diagnostics: false,
    }]
  },
};

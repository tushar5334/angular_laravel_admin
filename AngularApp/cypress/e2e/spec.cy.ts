describe('My First Test', () => {
  let baseUrl: string;
  before(() => {
    baseUrl = Cypress.env('baseUrl');
  });

  it('Visits the initial project page', () => {
    cy.visit(`${baseUrl}/login`);
    cy.contains('Material App');
  });
});

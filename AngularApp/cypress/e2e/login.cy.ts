describe('Test Login functionality', () => {
  let baseUrl: string;
  before(() => {
    baseUrl = Cypress.env('baseUrl');
  });

  it('Visits the login page', () => {
    cy.visit(`${baseUrl}/login`);
    //cy.customCommand('test param');
  });

  it('Sign in the user to check client side validation', () => {
    cy.get('form').submit();
    cy.contains('Email is required');
    cy.contains('Password is required');
  });

  it('Sign in the user to check valid email validation', () => {
    cy.get('[name="email"]').type('admin.in', { delay: 100 });
    cy.contains('Enter valid email');
  });

  it('Sign in the user to check wrong email validation', () => {
    cy.clearLoginInputs();

    cy.get('[name="email"]').type('test@test.com', { delay: 100 });
    cy.get('[name="password"]').type('password', { delay: 100 });
    cy.get('form').submit();

    cy.contains('Wrong email or password');
  });

  it('Sign in the user to check wrong password validation', () => {
    cy.clearLoginInputs();

    cy.get('[name="email"]').type('admin@admin.com', { delay: 100 });
    cy.get('[name="password"]').type('password123', { delay: 100 });
    cy.get('form').submit();

    cy.contains('Wrong email or password');
  });

  it('Sign in the user', () => {
    cy.clearLoginInputs();

    cy.get('[name="email"]').type('admin@admin.com', { delay: 100 });
    cy.get('[name="password"]').type('password', { delay: 100 });
    cy.get('form').submit();
  });
});

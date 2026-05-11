export function extractBackendErrors(error: any): string[] {
  const errors: string[] = [];

  if (error?.error?.errors) {
    Object.values(error.error.errors).forEach((messages) => {
      if (Array.isArray(messages)) {
        messages.forEach((message) => errors.push(String(message)));
      }
    });
  }

  if (errors.length > 0) {
    return errors;
  }

  if (error?.error?.message) {
    return [String(error.error.message)];
  }

  return ['Ha ocurrido un error inesperado.'];
}